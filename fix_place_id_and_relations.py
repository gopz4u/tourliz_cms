import os
import re

root_dir = 'c:/xampp/htdocs/tourliz_cms/app'

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # Replace 'place' => function with 'destination' => function
    new_content = re.sub(r"'place'\s*=>\s*function", "'destination' => function", content)
    # Replace ->where('place_id' with ->where('destination_id'
    new_content = re.sub(r"where\('place_id'", "where('destination_id'", new_content)
    # Replace ->place_id with ->destination_id
    new_content = re.sub(r"->place_id", "->destination_id", new_content)
    # Replace has('place_id') with has('destination_id')
    new_content = re.sub(r"has\('place_id'\)", "has('destination_id')", new_content)
    # Replace query('place_id') with query('destination_id') in some cases but API controller already expects both.
    
    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f'Updated {filepath}')

for root, dirs, files in os.walk(root_dir):
    for file in files:
        if file.endswith('.php'):
            process_file(os.path.join(root, file))

print('Done!')
