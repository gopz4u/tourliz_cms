import sys
import re

filename = r'c:\xampp\htdocs\tourliz_cms\resources\views\admin\packages\create.blade.php'
with open(filename, 'r', encoding='utf-8') as f:
    content = f.read()

# Find escaped backticks and dollar signs
# But be careful not to break legitimate escapes if any
# The error reported was $(\`.amenity-item[data-amenity-id="\${amenityId}"]\`);

fixed_content = content.replace('\\`', '`').replace('\\${', '${')

if fixed_content != content:
    with open(filename, 'w', encoding='utf-8') as f:
        f.write(fixed_content)
    print("Fixed escaped backticks/dollar signs.")
else:
    print("No escaped backticks found.")
