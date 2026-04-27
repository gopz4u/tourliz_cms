import re

file_path = r'c:\xampp\htdocs\tourliz_cms\resources\views\admin\b2b\edit.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Pattern for words split by 2+ spaces
# e.g. "func      tion"
# But we must avoid splitting things like "a - b"
# We only target letters split by spaces.

def fix_split_words(text):
    # This regex looks for letters split by spaces/tabs
    # but only if neither side is a common standalone word (like 'a', 'I', 'the')
    # Or just target cases where it looks like a single word was meant.
    
    # Common JS keywords and identifiers in this file
    keywords = ['function', 'return', 'const', 'let', 'window', 'itinerary', 'document', 'calculateDynamicTotal', 'renderBuilder', 'ensureArray', 'safeFloat', 'createDayCard', 'renderHotels', 'renderListItems', 'updateField', 'addItem', 'removeItem', 'addDay', 'removeDay']
    
    for kw in keywords:
        # Create regex for each keyword split by \s+
        for i in range(1, len(kw)):
            p1 = kw[:i]
            p2 = kw[i:]
            pattern = rf'\b{p1}\s+{p2}\b'
            text = re.sub(pattern, kw, text)
            
    # Also clean up massive spaces again just in case
    text = re.sub(r'[ \t]{5,}', ' ', text)
    return text

def clean_script(match):
    script_content = match.group(1)
    script_content = fix_split_words(script_content)
    return f"<script>{script_content}</script>"

new_content = re.sub(r"<script>(.*?)<\/script>", clean_script, content, flags=re.DOTALL)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(new_content)

print("Second aggressive cleanup complete.")
