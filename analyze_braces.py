
def analyze_braces_detailed(file_path, start_line, end_line):
    with open(file_path, 'r', encoding='utf-8') as f:
        lines = f.readlines()
    
    depth = 0
    for i in range(start_line - 1, end_line):
        line = lines[i]
        line_num = i + 1
        
        open_b = line.count('{')
        close_b = line.count('}')
        
        depth += open_b - close_b
        
        if open_b != 0 or close_b != 0:
            print(f"L{line_num}: Open={open_b}, Close={close_b}, NewDepth={depth}")

analyze_braces_detailed('c:/xampp/htdocs/tourliz_cms/resources/views/admin/b2b/edit.blade.php', 436, 1418)
