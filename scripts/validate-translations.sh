#!/bin/bash

# =============================================================================
# MULTILINGUAL SYSTEM VALIDATION SCRIPT
# =============================================================================

echo "🌍 MULTILINGUAL SYSTEM VALIDATION"
echo "=================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to check if a language file is properly translated
validate_language_file() {
    local lang=$1
    local file="lang/${lang}.json"
    
    if [[ ! -f $file ]]; then
        echo -e "${RED}❌ ${lang}: File not found${NC}"
        return 1
    fi
    
    local size=$(stat -c%s "$file" 2>/dev/null || stat -f%z "$file" 2>/dev/null)
    local lines=$(wc -l < "$file")
    
    echo -e "${BLUE}📊 ${lang}:${NC}"
    echo "   Size: $(ls -lh $file | awk '{print $5}')"
    echo "   Lines: $lines"
    
    # Check if file contains English text (indicates incomplete translation)
    local english_patterns=("These credentials do not match" "The provided password" "Too many login attempts")
    local has_english=false
    
    for pattern in "${english_patterns[@]}"; do
        if grep -q "$pattern" "$file"; then
            has_english=true
            break
        fi
    done
    
    if [[ $has_english == true ]]; then
        echo -e "   Status: ${YELLOW}⚠️  Contains English text (needs re-translation)${NC}"
        return 2
    elif [[ $size -lt 50000 ]]; then
        echo -e "   Status: ${YELLOW}⚠️  File too small (needs completion)${NC}"
        return 2
    else
        echo -e "   Status: ${GREEN}✅ Complete${NC}"
        return 0
    fi
}

echo ""
echo "🔍 LANGUAGE FILE ANALYSIS"
echo "========================"

# Reference file
echo -e "${BLUE}📋 Reference File:${NC}"
validate_language_file "en"

echo ""
echo -e "${BLUE}📋 Complete Languages:${NC}"
complete_langs=("es" "zh" "pt" "ru" "ar" "fr" "de" "tr")
for lang in "${complete_langs[@]}"; do
    validate_language_file "$lang"
done

echo ""
echo -e "${BLUE}📋 Languages Needing Work:${NC}"
incomplete_langs=("hi" "ja" "it")
needs_work=()
for lang in "${incomplete_langs[@]}"; do
    validate_language_file "$lang"
    if [[ $? -eq 2 ]]; then
        needs_work+=($lang)
    fi
done

echo ""
echo "🎯 SUMMARY"
echo "=========="

if [[ ${#needs_work[@]} -eq 0 ]]; then
    echo -e "${GREEN}✅ All languages are complete!${NC}"
    exit 0
else
    echo -e "${YELLOW}⚠️  Languages needing translation: ${needs_work[*]}${NC}"
    echo ""
    echo "📋 Recommended actions:"
    for lang in "${needs_work[@]}"; do
        case $lang in
            "hi")
                echo "   • Hindi: Complete translation (expand from ~200 to 2,500+ keys)"
                ;;
            "ja")
                echo "   • Japanese: Re-translate from English to Japanese"
                ;;
            "it")
                echo "   • Italian: Re-translate from English to Italian"
                ;;
        esac
    done
    
    echo ""
    echo "🚀 Ready to execute:"
    echo "   php artisan ai-translator:translate-parallel --locale=$(IFS=,; echo "${needs_work[*]}")"
fi

echo ""
echo "📊 SYSTEM STATUS"
echo "==============="
echo "Total languages: 12"
echo "Complete languages: $((12 - ${#needs_work[@]}))"
echo "Pending languages: ${#needs_work[@]}"
echo "Completion: $(( (12 - ${#needs_work[@]}) * 100 / 12 ))%" 