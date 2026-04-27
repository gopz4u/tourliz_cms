import re

file_path = r'c:\xampp\htdocs\tourliz_cms\resources\views\admin\b2b\edit.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

for match in re.finditer(r"<script>(.*?)<\/script>", content, re.DOTALL):
    script = match.group(1)
    # Look for standalone 'n' or other single letters that shouldn't be there
    # or broken patterns
    lines = script.split('\n')
    for i, line in enumerate(lines):
        # Look for pattern like "something n something" where it's not a known case
        # Exclude common uses of n (like in math or for loop if any, but this JS uses forEach)
        # The error says "Unexpected identifier 'n'"
        if re.search(r'\s+n\s+', line) and 'const n =' not in line and 'NaN(n)' not in line:
            print(f"Suspicious 'n' at line {i+1} of some script block: {line.strip()}")

print("Done.")
