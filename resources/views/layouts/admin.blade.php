<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tourliz CMS') - Admin Panel</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #2c3e50;
            --sidebar-active: #3498db;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            padding: 20px 0;
            z-index: 1000;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-brand {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        
        .logo-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            color: white;
        }
        
        .logo-icon {
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }
        
        .logo-icon .triangle-blue {
            width: 0;
            height: 0;
            border-left: 10px solid #3b82f6;
            border-top: 7px solid transparent;
            border-bottom: 7px solid transparent;
        }
        
        .logo-icon .triangle-red {
            width: 0;
            height: 0;
            border-left: 10px solid #ef4444;
            border-top: 7px solid transparent;
            border-bottom: 7px solid transparent;
            margin-left: -10px;
        }
        
        .logo-text {
            font-size: 1.3rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .logo-text .letter-i {
            display: inline-block;
            position: relative;
        }
        
        .logo-text .letter-i .dot {
            width: 8px;
            height: 8px;
            background: #3b82f6;
            border-radius: 1px;
            position: absolute;
            top: -5px;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .logo-text .letter-i .bar {
            width: 3px;
            height: 18px;
            background: #ef4444;
            border-radius: 1px;
            margin: 0 auto;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-menu li {
            margin: 5px 0;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: var(--sidebar-active);
            color: white;
        }
        
        .sidebar-menu a i {
            margin-right: 10px;
            width: 20px;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
        }
        
        .page-header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .btn-action {
            margin: 0 2px;
        }
        
        .table-responsive {
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .badge-status {
            padding: 6px 12px;
            border-radius: 20px;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}" class="logo-brand">
                <div class="logo-icon">
                    <div class="triangle-blue"></div>
                    <div class="triangle-red"></div>
                </div>
                <div class="logo-text">
                    TOUR<span class="letter-i">
                        <span class="dot"></span>
                        <span class="bar"></span>
                    </span>LIZ
                </div>
            </a>
            <div style="font-size: 0.7rem; color: rgba(255,255,255,0.7); margin-top: 5px;">CMS Admin</div>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.places.index') }}" class="{{ request()->routeIs('admin.places.*') ? 'active' : '' }}">
                    <i class="bi bi-geo-alt"></i>
                    <span>Places</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.packages.index') }}" class="{{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                    <i class="bi bi-briefcase"></i>
                    <span>Packages</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.services.index') }}" class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                    <i class="bi bi-tools"></i>
                    <span>Services</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Users</span>
                </a>
            </li>
            <li style="margin-top: auto; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: rgba(255,255,255,0.8); width: 100%; text-align: left; padding: 12px 20px; cursor: pointer; display: flex; align-items: center; transition: all 0.3s;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'; this.style.color='white';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgba(255,255,255,0.8)';">
                        <i class="bi bi-box-arrow-right" style="margin-right: 10px; width: 20px;"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Quill Rich Text Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    
    <style>
        .ql-container {
            min-height: 200px;
            font-size: 14px;
        }
        .ql-editor {
            min-height: 200px;
        }
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 4px;
        }
    </style>
    
    <script>
        // CSRF Token setup for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Initialize Quill Editor
        function initQuillEditor(selector, height = 300) {
            if (typeof Quill !== 'undefined') {
                var quill = new Quill(selector, {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            [{ 'align': [] }],
                            ['link', 'image'],
                            ['clean']
                        ]
                    },
                    placeholder: 'Start typing...'
                });
                
                // Handle image upload in Quill
                quill.getModule('toolbar').addHandler('image', function() {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    input.click();
                    
                    input.onchange = function() {
                        var file = input.files[0];
                        if (file) {
                            var formData = new FormData();
                            formData.append('image', file);
                            
                            $.ajax({
                                url: '{{ route("admin.upload.image") }}',
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    if (response.success && response.url) {
                                        var range = quill.getSelection(true);
                                        quill.insertEmbed(range.index, 'image', response.url);
                                    }
                                },
                                error: function() {
                                    alert('Error uploading image');
                                }
                            });
                        }
                    };
                });
                
                return quill;
            }
            return null;
        }
    </script>
    
    @stack('scripts')
</body>
</html>

