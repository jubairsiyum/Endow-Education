/**
 * Notification System Integration
 * 
 * This file demonstrates how to integrate the notification API
 * with your existing admin layout notification modal.
 */

class NotificationManager {
    constructor() {
        this.apiUrl = '/api/notifications';
        this.unreadCount = 0;
        this.pollingInterval = 30000; // Poll every 30 seconds
        this.init();
    }

    init() {
        this.loadNotifications();
        this.startPolling();
        this.bindEvents();
    }

    /**
     * Load unread notifications
     */
    async loadNotifications() {
        try {
            const response = await fetch(`${this.apiUrl}/unread?limit=10`, {
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                const data = await response.json();
                console.log('Notifications loaded:', data);
                this.updateNotificationUI(data.data);
                this.updateBadge(data.count);
            } else {
                console.error('Failed to load notifications. Status:', response.status);
                this.showErrorState('Failed to load notifications');
            }
        } catch (error) {
            console.error('Failed to load notifications:', error);
            this.showErrorState('Unable to connect to server');
        }
    }

    /**
     * Get unread count
     */
    async getUnreadCount() {
        try {
            const response = await fetch(`${this.apiUrl}/unread-count`, {
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                const data = await response.json();
                console.log('Unread count:', data.count);
                this.updateBadge(data.count);
            } else {
                console.error('Failed to get unread count. Status:', response.status);
            }
        } catch (error) {
            console.error('Failed to get unread count:', error);
        }
    }

    /**
     * Mark notification as read
     */
    async markAsRead(notificationId) {
        try {
            const response = await fetch(`${this.apiUrl}/${notificationId}/mark-read`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.updateBadge(data.unread_count);
                return true;
            }
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
        return false;
    }

    /**
     * Mark all notifications as read
     */
    async markAllAsRead() {
        try {
            const response = await fetch(`${this.apiUrl}/mark-all-read`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                this.updateBadge(0);
                this.loadNotifications();
                return true;
            }
        } catch (error) {
            console.error('Failed to mark all as read:', error);
        }
        return false;
    }

    /**
     * Delete notification
     */
    async deleteNotification(notificationId) {
        try {
            const response = await fetch(`${this.apiUrl}/${notificationId}`, {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.updateBadge(data.unread_count);
                return true;
            }
        } catch (error) {
            console.error('Failed to delete notification:', error);
        }
        return false;
    }

    /**
     * Update notification UI
     */
    updateNotificationUI(notifications) {
        const container = document.getElementById('notification-list');
        if (!container) return;

        if (notifications.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-check-circle fa-3x mb-3" style="opacity: 0.3; color: #10B981;"></i>
                    <p class="mb-1 fw-semibold">You're all caught up! 🎉</p>
                    <p class="mb-0" style="font-size: 13px;">No new notifications right now</p>
                </div>
            `;
            return;
        }

        container.innerHTML = notifications.map(notification => {
            const data = notification.data;
            const timeAgo = this.formatTimeAgo(notification.created_at);
            
            return `
                <a href="${data.url}" 
                   class="notification-item ${notification.read_at ? 'read' : 'unread'}" 
                   data-id="${notification.id}"
                   onclick="notificationManager.handleNotificationClick(event, '${notification.id}', '${data.url}')">
                    <div class="notification-icon ${data.color || 'primary'}">
                        <i class="${data.icon || 'fas fa-bell'}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${data.title}</div>
                        <div class="notification-message">${data.message}</div>
                        <div class="notification-time">${timeAgo}</div>
                    </div>
                    ${!notification.read_at ? '<div class="notification-dot"></div>' : ''}
                </a>
            `;
        }).join('');
    }

    /**
     * Update badge count
     */
    updateBadge(count) {
        this.unreadCount = count;
        const badge = document.getElementById('notification-badge');
        
        if (badge) {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }
    }

    /**
     * Show error state in notification dropdown
     */
    showErrorState(message) {
        const container = document.getElementById('notification-list');
        if (!container) return;

        container.innerHTML = `
            <div class="text-center py-5 text-muted">
                <i class="fas fa-exclamation-circle fa-3x mb-3" style="opacity: 0.3; color: #dc3545;"></i>
                <p class="mb-1 fw-semibold">Oops! Something went wrong</p>
                <p class="mb-0" style="font-size: 13px;">${message}</p>
            </div>
        `;
    }

    /**
     * Handle notification click
     */
    async handleNotificationClick(event, notificationId, url) {
        event.preventDefault();
        
        // Mark as read
        await this.markAsRead(notificationId);
        
        // Navigate to URL
        window.location.href = url;
    }

    /**
     * Format time ago
     */
    formatTimeAgo(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        if (seconds < 60) return 'Just now';
        if (seconds < 3600) return `${Math.floor(seconds / 60)} minutes ago`;
        if (seconds < 86400) return `${Math.floor(seconds / 3600)} hours ago`;
        if (seconds < 604800) return `${Math.floor(seconds / 86400)} days ago`;
        
        return date.toLocaleDateString();
    }

    /**
     * Start polling for new notifications
     */
    startPolling() {
        setInterval(() => {
            this.getUnreadCount();
        }, this.pollingInterval);
    }

    /**
     * Bind event listeners
     */
    bindEvents() {
        // Notification bell toggle
        const notificationBell = document.querySelector('.notification-bell');
        const notificationWrapper = document.querySelector('.notification-wrapper');
        
        if (notificationBell && notificationWrapper) {
            notificationBell.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                notificationWrapper.classList.toggle('show');
                
                // Load notifications when opening
                if (notificationWrapper.classList.contains('show')) {
                    this.loadNotifications();
                }
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (notificationWrapper && !notificationWrapper.contains(e.target)) {
                notificationWrapper.classList.remove('show');
            }
        });

        // Prevent dropdown from closing when clicking inside
        const notificationDropdown = document.querySelector('.notification-dropdown');
        if (notificationDropdown) {
            notificationDropdown.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }

        // Mark all as read button
        const markAllBtn = document.getElementById('mark-all-read-btn');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', async (e) => {
                e.preventDefault();
                if (await this.markAllAsRead()) {
                    this.loadNotifications();
                }
            });
        }

        // Refresh notifications button
        const refreshBtn = document.getElementById('refresh-notifications-btn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.loadNotifications();
            });
        }
    }
}
