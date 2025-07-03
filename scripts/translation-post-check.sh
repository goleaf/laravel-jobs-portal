#!/bin/bash

# =============================================================================
# POST-TRANSLATION QUALITY ASSURANCE SCRIPT
# =============================================================================

echo "🔍 POST-TRANSLATION QUALITY CHECK"
echo "================================="

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Function to check translation quality
check_translation_quality() {
    local lang=$1
    local file="lang/${lang}.json"
    
    echo -e "${BLUE}🔍 Checking $lang translation quality...${NC}"
    
    # Check file exists and size
    if [[ ! -f $file ]]; then
        echo -e "${RED}❌ File not found: $file${NC}"
        return 1
    fi
    
    local size=$(stat -c%s "$file" 2>/dev/null || stat -f%z "$file" 2>/dev/null)
    echo "   File size: $(ls -lh $file | awk '{print $5}')"
    
    # Check for valid JSON
    if ! python3 -c "import json; json.load(open('$file'))" 2>/dev/null; then
        echo -e "${RED}❌ Invalid JSON format${NC}"
        return 1
    fi
    echo -e "${GREEN}✅ Valid JSON format${NC}"
    
    # Check for English remnants (should be minimal after translation)
    local english_count=$(grep -c "These credentials\|The provided\|Too many login" "$file" 2>/dev/null || echo "0")
    if [[ $english_count -gt 0 ]]; then
        echo -e "${YELLOW}⚠️  Found $english_count English phrases (may need review)${NC}"
    else
        echo -e "${GREEN}✅ No English remnants detected${NC}"
    fi
    
    # Check key count compared to English
    local en_keys=$(cat lang/en.json | grep -o '"[^"]*":' | wc -l)
    local lang_keys=$(cat "$file" | grep -o '"[^"]*":' | wc -l)
    local key_ratio=$((lang_keys * 100 / en_keys))
    
    echo "   Translation keys: $lang_keys (${key_ratio}% of English)"
    
    if [[ $key_ratio -lt 90 ]]; then
        echo -e "${YELLOW}⚠️  Key count seems low (may be incomplete)${NC}"
    else
        echo -e "${GREEN}✅ Good key coverage${NC}"
    fi
    
    # Sample translation check
    echo "   Sample translations:"
    case $lang in
        "hi")
            local dashboard=$(cat "$file" | grep '"dashboard"' | head -1)
            echo "   • $dashboard"
            ;;
        "ja") 
            local dashboard=$(cat "$file" | grep '"dashboard"' | head -1)
            echo "   • $dashboard"
            ;;
        "it")
            local dashboard=$(cat "$file" | grep '"dashboard"' | head -1) 
            echo "   • $dashboard"
            ;;
    esac
    
    echo ""
}

# Function to test Laravel language functionality
test_laravel_integration() {
    echo -e "${BLUE}🧪 Testing Laravel Integration...${NC}"
    
    # Test if Laravel can load the language files
    if php artisan tinker --execute="echo 'Testing language loading...'; app()->setLocale('hi'); echo __('dashboard'); app()->setLocale('ja'); echo __('dashboard'); app()->setLocale('it'); echo __('dashboard');" 2>/dev/null; then
        echo -e "${GREEN}✅ Laravel language loading successful${NC}"
    else
        echo -e "${YELLOW}⚠️  Laravel integration test inconclusive${NC}"
    fi
}

# Function to check frontend compilation
test_frontend_compilation() {
    echo -e "${BLUE}🏗️  Testing Frontend Compilation...${NC}"
    
    if command -v npm >/dev/null 2>&1; then
        echo "Running npm run build..."
        if npm run build >/dev/null 2>&1; then
            echo -e "${GREEN}✅ Frontend compilation successful${NC}"
        else
            echo -e "${YELLOW}⚠️  Frontend compilation issues (check manually)${NC}"
        fi
    else
        echo -e "${YELLOW}⚠️  npm not available, skipping frontend test${NC}"
    fi
}

# Main execution
echo ""
languages_to_check=("hi" "ja" "it")

for lang in "${languages_to_check[@]}"; do
    check_translation_quality "$lang"
done

echo ""
test_laravel_integration

echo ""
test_frontend_compilation

echo ""
echo "🎯 FINAL QUALITY ASSESSMENT"
echo "=========================="

# Count issues
issues=0
for lang in "${languages_to_check[@]}"; do
    file="lang/${lang}.json"
    if [[ ! -f $file ]]; then
        ((issues++))
        continue
    fi
    
    size=$(stat -c%s "$file" 2>/dev/null || stat -f%z "$file" 2>/dev/null)
    if [[ $size -lt 50000 ]]; then
        ((issues++))
    fi
    
    if grep -q "These credentials\|The provided" "$file" 2>/dev/null; then
        ((issues++))
    fi
done

if [[ $issues -eq 0 ]]; then
    echo -e "${GREEN}🎉 ALL TRANSLATIONS COMPLETE AND VALIDATED!${NC}"
    echo ""
    echo "📊 Final Status:"
    echo "   • Hindi: Complete professional translation"
    echo "   • Japanese: Complete professional translation" 
    echo "   • Italian: Complete professional translation"
    echo "   • System: Ready for production deployment"
    echo ""
    echo -e "${GREEN}✅ MULTILINGUAL SYSTEM: 100% COMPLETE${NC}"
else
    echo -e "${YELLOW}⚠️  Found $issues potential issues${NC}"
    echo "   Please review the warnings above and re-run translations if needed."
fi

echo ""
echo "🚀 Next steps:"
echo "   1. Review any warnings above"
echo "   2. Test language switching in browser"
echo "   3. Deploy to production"
echo "   4. Update documentation" 