import sys

filename = r'c:\xampp\htdocs\tourliz_cms\resources\views\admin\packages\create.blade.php'
with open(filename, 'r', encoding='utf-8') as f:
    lines = f.readlines()

# handleSupplierChange starts around 1298
start = 1297
end = 1417
sub_lines = lines[start:end]

l_braces = 0
r_braces = 0
l_parens = 0
r_parens = 0

for i, line in enumerate(sub_lines, start + 1):
    l_b = line.count('{')
    r_b = line.count('}')
    l_p = line.count('(')
    r_p = line.count(')')
    l_braces += l_b
    r_braces += r_b
    l_parens += l_p
    r_parens += r_p
    if l_b != r_b or l_p != r_p:
        print(f"Line {i}: Braces ({l_b}/{r_b}), Parens ({l_p}/{r_p}) -> {line.strip()}")

print(f"Total: Braces ({l_braces}/{r_braces}), Parens ({l_parens}/{r_parens})")
