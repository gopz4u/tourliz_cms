import os
import re

root_dir = 'c:/xampp/htdocs/tourliz_cms/app'
views_dir = 'c:/xampp/htdocs/tourliz_cms/resources/views'

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    new_content = re.sub(r"with\('place'\)", "with('destination')", content)
    new_content = re.sub(r"with\(\['place'\]\)", "with(['destination'])", new_content)
    new_content = re.sub(r"->place([^\w\d_])", r"->destination\1", new_content)

    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f'Updated {filepath}')

for r_dir in [root_dir, views_dir]:
    for root, dirs, files in os.walk(r_dir):
        for file in files:
            if file.endswith('.php'):
                try:
                    process_file(os.path.join(root, file))
                except Exception as e:
                    pass

print('Done!')
