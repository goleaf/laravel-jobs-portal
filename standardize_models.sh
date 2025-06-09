#!/bin/bash

# Script to standardize Laravel models with consistent scopes and casts

echo "Starting model standardization process..."

# Directory containing the models
MODEL_DIR="app/Models"

# Check if the model directory exists
if [ ! -d "$MODEL_DIR" ]; then
    echo "Error: Model directory $MODEL_DIR does not exist."
    exit 1
fi

# Iterate through all PHP files in the model directory
for model_file in "$MODEL_DIR"/*.php; do
    if [ -f "$model_file" ]; then
        echo "Processing: $model_file"
        
        # Check if the file uses the old $casts property instead of casts() method
        if grep -q "protected \$casts =" "$model_file"; then
            echo "  - Found old casts property. Updating to casts() method..."
            # Backup the original file
            cp "$model_file" "${model_file}.backup"
            # Use sed to replace the old casts property with the new casts() method
            sed -i 's/protected \$casts = \[/protected function casts(): array\n    {\n        return [/g' "$model_file"
            sed -i 's/];/];\n    }/g' "$model_file"
            echo "  - Updated casts in $model_file."
        else
            echo "  - Casts already updated or not present."
        fi
        
        # Check for essential scopes (active, inactive, recent, search)
        if ! grep -q "scopeActive(" "$model_file"; then
            echo "  - Missing active scope. Adding..."
            # Backup the original file if not already done
            if [ ! -f "${model_file}.backup" ]; then
                cp "$model_file" "${model_file}.backup"
            fi
            # Append the active scope
            echo "
    /**
     * Scope a query to only include active records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  \$query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(\$query)
    {
        return \$query->where('is_active', true);
    }" >> "$model_file"
            echo "  - Added active scope to $model_file"
        else
            echo "  - Active scope already present."
        fi
        
        if ! grep -q "scopeInactive(" "$model_file"; then
            echo "  - Missing inactive scope. Adding..."
            # Backup the original file if not already done
            if [ ! -f "${model_file}.backup" ]; then
                cp "$model_file" "${model_file}.backup"
            fi
            # Append the inactive scope
            echo "
    /**
     * Scope a query to only include inactive records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  \$query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive(\$query)
    {
        return \$query->where('is_active', false);
    }" >> "$model_file"
            echo "  - Added inactive scope to $model_file"
        else
            echo "  - Inactive scope already present."
        fi
        
        if ! grep -q "scopeRecent(" "$model_file"; then
            echo "  - Missing recent scope. Adding..."
            # Backup the original file if not already done
            if [ ! -f "${model_file}.backup" ]; then
                cp "$model_file" "${model_file}.backup"
            fi
            # Append the recent scope
            echo "
    /**
     * Scope a query to only include recent records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  \$query
     * @param  int  \$days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent(\$query, int \$days = 30)
    {
        return \$query->where('created_at', '>=', \Carbon\Carbon::now()->subDays(\$days));
    }" >> "$model_file"
            echo "  - Added recent scope to $model_file"
        else
            echo "  - Recent scope already present."
        fi
        
        if ! grep -q "scopeSearch(" "$model_file"; then
            echo "  - Missing search scope. Adding..."
            # Backup the original file if not already done
            if [ ! -f "${model_file}.backup" ]; then
                cp "$model_file" "${model_file}.backup"
            fi
            # Append the search scope
            echo "
    /**
     * Scope a query to search records by name or relevant fields.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  \$query
     * @param  string  \$search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch(\$query, \$search)
    {
        return \$query->where('name', 'like', '%' . \$search . '%');
    }" >> "$model_file"
            echo "  - Added search scope to $model_file"
        else
            echo "  - Search scope already present."
        fi
    else
        echo "No PHP files found in $MODEL_DIR"
    fi
done

echo "Model standardization process completed. Backup files created for modified models." 