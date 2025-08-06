import os

def print_directory_structure(startpath, ignore_dirs=None, ignore_files=None, level=0):
    if ignore_dirs is None:
        ignore_dirs = ['__pycache__', '.git', 'venv', 'env', 'node_modules','vendor']
    if ignore_files is None:
        ignore_files = ['.env', '*.pyc', '*.egg-info']

    for item in sorted(os.listdir(startpath)):
        path = os.path.join(startpath, item)
        if any(ignore in item for ignore in ignore_dirs) or any(
            path.endswith(ext) for ext in ignore_files
        ):
            continue
        print('  ' * level + '├── ' + item)
        if os.path.isdir(path):
            print_directory_structure(path, ignore_dirs, ignore_files, level + 1)

if __name__ == '__main__':
    project_root = '.'  # Current directory
    print('.')
    print_directory_structure(project_root)