
# =============================================================
# LANGUAGE FILE ANALYSIS - CURRENT STATE
# =============================================================

echo 'Current Language File Sizes:'
ls -lh lang/*.json | awk '{print $9, $5}' | sed 's/lang\///g' | sort

echo -e '
Detailed Translation Status:'
echo 'Hindi (needs completion):' 
wc -l lang/hi.json
echo 'Japanese (needs re-translation):' 
head -5 lang/ja.json | grep -E '".*".*".*"' | head -2
echo 'Italian (needs re-translation):' 
head -5 lang/it.json | grep -E '".*".*".*"' | head -2

echo -e '
English Master File (reference):' 
wc -l lang/en.json

