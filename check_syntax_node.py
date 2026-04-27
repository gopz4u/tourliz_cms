
import re
import subprocess

def check_syntax(file_path):
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    scripts = re.findall(r'<script>(.*?)</script>', content, re.DOTALL)
    
    for i, script in enumerate(scripts):
        # Improved replacement: replace with a simple variable name or string
        clean = script
        clean = re.sub(r'\{\{.*?\}\}', ' "BLADE_VAR" ', clean)
        clean = re.sub(r'\{!!.*?!!\}', ' "BLADE_VAR_RAW" ', clean)
        clean = re.sub(r'@json\(.*?\)', ' [] ', clean)
        
        with open(f'temp_script_{i}.js', 'w', encoding='utf-8') as f_temp:
            f_temp.write(clean)
        
        print(f"--- Checking Script Block {i} ---")
        res = subprocess.run(['node', '--check', f'temp_script_{i}.js'], capture_output=True, text=True)
        if res.returncode != 0:
            print(res.stderr)
            # Print lines around error
            match = re.search(r'temp_script_\d+\.js:(\d+)', res.stderr)
            if match:
                err_line = int(match.group(1))
                lines = clean.splitlines()
                start = max(0, err_line - 5)
                end = min(len(lines), err_line + 5)
                for j in range(start, end):
                    prefix = ">>" if j + 1 == err_line else "  "
                    print(f"{prefix} {j+1}: {lines[j]}")
        else:
            print(f"Block {i} is valid.")

check_syntax('c:/xampp/htdocs/tourliz_cms/resources/views/admin/b2b/edit.blade.php')
