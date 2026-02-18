<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Task Management System</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
        <style>
            .notification-dropdown {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }
            .notification-item {
                padding: 12px 16px;
                border-left: 3px solid transparent;
                background-color: white;
                transition: all 0.2s ease;
            }
            .notification-item:hover {
                background-color: #f8f9fa;
            }
            .notification-item.unread {
                background-color: #f0f8ff;
                border-left-color: #007bff;
            }
            .notification-item.unread:hover {
                background-color: #e3f2fd;
            }
            .notification-title {
                font-weight: 600;
                font-size: 0.9rem;
                margin-bottom: 4px;
                color: #333;
            }
            .notification-message {
                font-size: 0.8rem;
                color: #666;
                margin-bottom: 4px;
                line-height: 1.3;
            }
            .notification-time {
                font-size: 0.7rem;
                color: #999;
            }
            .notification-actions {
                display: flex;
                gap: 8px;
                margin-top: 8px;
            }
            .notification-actions .btn {
                font-size: 0.7rem;
                padding: 2px 8px;
            }
            #notification-badge {
                font-size: 0.65rem !important;
                padding: 2px 5px !important;
            }

            /* Responsive navigation improvements */
            @media (max-width: 768px) {
                .notification-dropdown {
                    width: 300px !important;
                    left: -250px !important;
                }
            }
        </style>
    </head>
    <body class="d-flex flex-column min-vh-100">
        <!-- Responsive navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container px-5">
                <a class="navbar-brand" href="#!">TaskEase</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('tasks.index') }}">Task</a></li>
                        @auth
                            @if(auth()->user()->isCustomer())
                                <li class="nav-item"><a class="nav-link" href="{{ route('projects.index') }}">Project</a></li>
                            @endif
                        @endauth
                    </ul>
                    
                    @auth
                    <!-- Search Bar -->
                    <form class="d-flex me-3" method="GET" action="{{ route('search') }}" role="search">
                        <input type="search" name="search" placeholder="Search..." 
                               aria-label="Search" value="{{ request('search') }}" 
                               style="
                                   background: transparent; 
                                   border: none; 
                                   border-bottom: 1px solid rgba(255,255,255,0.5); 
                                   color: white; 
                                   width: 150px; 
                                   padding: 8px 0; 
                                   margin-right: 10px;
                                   outline: none;
                               "
                               onfocus="this.style.borderBottomColor='rgba(255,255,255,0.8)'"
                               onblur="this.style.borderBottomColor='rgba(255,255,255,0.5)'">
                        <button class="btn btn-outline-light" type="submit" style="border: none; background: transparent; color: rgba(255,255,255,0.8);">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>

                    <!-- Notifications -->
                    <div class="dropdown me-3">
                        <button class="btn btn-outline-light position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border: none; background: transparent;">
                            <i class="bi bi-bell" style="font-size: 1.2rem;"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notification-badge" style="display: none; font-size: 0.7rem;">
                                0
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                            <li class="dropdown-header d-flex justify-content-between align-items-center">
                                <span>Notifications</span>
                                <button class="btn btn-sm btn-link text-muted p-0" id="mark-all-read" title="Mark all as read">
                                    <i class="bi bi-check-all"></i>
                                </button>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <!-- Success/Error Messages for Notifications -->
                            <div id="notification-messages" style="display: none;"></div>
                            <div id="notification-list">
                                <li class="dropdown-item text-center text-muted py-3">
                                    <i class="bi bi-bell-slash"></i><br>
                                    No notifications
                                </li>
                            </div>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-center" href="{{ route('notifications.index') }}">
                                    <i class="bi bi-list"></i> View All Notifications
                                </a>
                            </li>
                        </ul>
                    </div>
                    @endauth
                    
                    @auth
                    <div class="d-flex">
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="nav-link btn" style="border: none; padding: 0; cursor: pointer; background: none; color: rgba(255,255,255,.55);" title="Logout">
                                <i class="bi bi-box-arrow-right" style="font-size: 1.5rem;"></i>
                            </button>
                        </form>
                    </div>
                    @endauth
            </div>
        </nav>
        <!-- Flash Messages -->
        <div class="container px-5 mt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
        <!-- Main content-->
        <main class="flex-grow-1">
            @yield('content')
        </main>
        <!-- Footer-->
        <footer class="py-4 bg-dark mt-auto">
            <div class="container px-5"><p class="m-0 text-center text-white">Copyright &copy; Task Management System 2026</p></div>
        </footer>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="{{ asset('js/scripts.js') }}"></script>
        
        <!-- Notification JavaScript -->
        <script>
            let notificationUpdateInterval;
            
            document.addEventListener('DOMContentLoaded', function() {
                // Load notifications on page load
                loadNotifications();
                
                // Set up auto-refresh every 30 seconds
                notificationUpdateInterval = setInterval(loadNotifications, 30000);
                
                // Mark all as read functionality
                document.getElementById('mark-all-read').addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    markAllAsRead();
                });
                
                // Stop the interval when the page is being unloaded
                window.addEventListener('beforeunload', function() {
                    if (notificationUpdateInterval) {
                        clearInterval(notificationUpdateInterval);
                    }
                });
            });
            
            function loadNotifications() {
                fetch('{{ route("notifications.recent") }}')
                    .then(response => response.json())
                    .then(data => {
                        updateNotificationBadge(data.unread_count);
                        updateNotificationList(data.notifications);
                    })
                    .catch(error => {
                        console.error('Error loading notifications:', error);
                    });
            }
            
            function updateNotificationBadge(count) {
                const badge = document.getElementById('notification-badge');
                
                if (typeof count === 'undefined') {
                    // If no count provided, calculate from current unread notifications
                    const unreadNotifications = document.querySelectorAll('.notification-item.unread');
                    count = unreadNotifications.length;
                }
                
                if (count > 0) {
                    badge.textContent = count > 10 ? '10+' : count;
                    badge.style.display = 'block';
                } else {
                    badge.style.display = 'none';
                }
            }
            
            function updateNotificationList(notifications) {
                const list = document.getElementById('notification-list');
                
                if (notifications.length === 0) {
                    list.innerHTML = `
                        <li class="dropdown-item text-center text-muted py-3">
                            <i class="bi bi-bell-slash"></i><br>
                            No notifications
                        </li>
                    `;
                    return;
                }
                
                let html = '';
                notifications.forEach(notification => {
                    const unreadClass = notification.is_read ? '' : 'unread';
                    const readIcon = notification.is_read ? 'bi-envelope-open' : 'bi-envelope';
                    
                    html += `
                        <li class="notification-item ${unreadClass}" data-notification-id="${notification.id}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="notification-title">${notification.title}</div>
                                    <div class="notification-message">${notification.message}</div>
                                    <div class="notification-time">
                                        <i class="bi ${readIcon}"></i> ${notification.time_ago}
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        ${!notification.is_read ? `
                                            <li><button class="dropdown-item" onclick="markAsReadFromElement(this); event.stopPropagation(); return false;">
                                                <i class="bi bi-check"></i> Mark as read
                                            </button></li>
                                        ` : ''}
                                        <li><button class="dropdown-item" onclick="viewNotificationFromElement(this); event.stopPropagation(); return false;">
                                            <i class="bi bi-eye"></i> View
                                        </button></li>
                                        <li><button class="dropdown-item text-danger" onclick="deleteNotificationFromElement(this); event.stopPropagation(); return false;">
                                            <i class="bi bi-trash"></i> Delete
                                        </button></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    `;
                });
                
                list.innerHTML = html;
            }
            
            function showNotificationMessage(message, type = 'success') {
                const messagesDiv = document.getElementById('notification-messages');
                
                // Check if the element exists, if not create it
                if (!messagesDiv) {
                    console.warn('notification-messages element not found, creating it...');
                    const notificationList = document.getElementById('notification-list');
                    if (notificationList) {
                        const messagesContainer = document.createElement('div');
                        messagesContainer.id = 'notification-messages';
                        messagesContainer.style.display = 'none';
                        notificationList.parentNode.insertBefore(messagesContainer, notificationList);
                    } else {
                        console.error('Cannot create notification-messages: notification-list not found');
                        return;
                    }
                }
                
                const targetDiv = document.getElementById('notification-messages');
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
                
                targetDiv.innerHTML = `
                    <li class="px-3 py-2">
                        <div class="alert ${alertClass} alert-dismissible fade show mb-0 py-2" role="alert">
                            <i class="bi ${icon}"></i> ${message}
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" onclick="this.closest('li').style.display='none'"></button>
                        </div>
                    </li>
                `;
                targetDiv.style.display = 'block';
                
                // Auto-hide after 5 seconds
                setTimeout(() => {
                    if (targetDiv) {
                        targetDiv.style.display = 'none';
                    }
                }, 5000);
            }
            
            function markAsRead(notificationId) {
                console.log('Attempting to mark as read notification:', notificationId);
                
                // Validate notification ID
                if (!notificationId || notificationId === 'undefined' || notificationId === 'null') {
                    console.error('Invalid notification ID:', notificationId);
                    alert('Error: Invalid notification ID');
                    return;
                }
                
                const baseUrl = '{{ url("/notifications") }}';
                const markReadUrl = `${baseUrl}/${notificationId}/mark-read`;
                console.log('Mark as read URL:', markReadUrl);
                
                fetch(markReadUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    console.log('MarkAsRead response status:', response.status);
                    if (response.ok) {
                        // Refresh the page to show success message and updated notifications
                        location.reload();
                    } else {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                    alert('Error marking notification as read');
                });
            }
            
            // Alternative function that gets ID from DOM element
            function markAsReadFromElement(element) {
                const notificationElement = element.closest('[data-notification-id]');
                if (notificationElement) {
                    const notificationId = notificationElement.getAttribute('data-notification-id');
                    console.log('Found notification ID from DOM for mark as read:', notificationId);
                    markAsRead(notificationId);
                } else {
                    console.error('Could not find notification element');
                    alert('Error: Could not identify notification to mark as read');
                }
            }
            
            function viewNotification(notificationId) {
                if (!notificationId || notificationId === 'undefined' || notificationId === 'null') {
                    console.error('Invalid notification ID:', notificationId);
                    alert('Error: Invalid notification ID');
                    return;
                }
                window.location.href = `{{ url('/notifications') }}/${notificationId}`;
            }
            
            // Alternative function that gets ID from DOM element
            function viewNotificationFromElement(element) {
                const notificationElement = element.closest('[data-notification-id]');
                if (notificationElement) {
                    const notificationId = notificationElement.getAttribute('data-notification-id');
                    console.log('Found notification ID from DOM for view:', notificationId);
                    viewNotification(notificationId);
                } else {
                    console.error('Could not find notification element');
                    alert('Error: Could not identify notification to view');
                }
            }
            
            function markAllAsRead() {
                if (confirm('Mark all notifications as read?')) {
                    fetch('{{ route("notifications.markAllAsRead") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('MarkAllAsRead response status:', response.status);
                        if (response.ok) {
                            // Refresh the page to show success message and updated notifications
                            location.reload();
                        } else {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                    })
                    .catch(error => {
                        console.error('Error marking all notifications as read:', error);
                        alert('Error marking notifications as read');
                    });
                }
            }
            
            function deleteNotification(notificationId) {
                console.log('Attempting to delete notification:', notificationId);
                
                // Validate notification ID
                if (!notificationId || notificationId === 'undefined' || notificationId === 'null') {
                    console.error('Invalid notification ID:', notificationId);
                    alert('Error: Invalid notification ID');
                    return;
                }
                
                if (confirm('Are you sure you want to delete this notification?')) {
                    // Construct the URL more safely
                    const baseUrl = '{{ url("/notifications") }}';
                    const deleteUrl = `${baseUrl}/${notificationId}`;
                    console.log('DELETE URL:', deleteUrl);
                    
                    fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('Delete response status:', response.status);
                        console.log('Delete response URL:', response.url);
                        
                        if (response.ok) {
                            // Refresh the page to show success message and updated notifications
                            location.reload();
                        } else {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting notification:', error);
                        alert('Error deleting notification: ' + error.message);
                    });
                }
            }
            
            // Alternative delete function that gets ID from DOM element
            function deleteNotificationFromElement(element) {
                const notificationElement = element.closest('[data-notification-id]');
                if (notificationElement) {
                    const notificationId = notificationElement.getAttribute('data-notification-id');
                    console.log('Found notification ID from DOM:', notificationId);
                    deleteNotification(notificationId);
                } else {
                    console.error('Could not find notification element');
                    alert('Error: Could not identify notification to delete');
                }
            }
            
            function deleteAllNotifications() {
                if (confirm('Are you sure you want to delete ALL notifications? This cannot be undone.')) {
                    fetch('{{ route("notifications.destroyAll") }}', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('Delete all response status:', response.status);
                        if (response.ok) {
                            // Refresh the page to show success message and updated notifications
                            location.reload();
                        } else {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting all notifications:', error);
                        alert('Error deleting all notifications');
                    });
                }
            }
        </script>
    </body>
</html>