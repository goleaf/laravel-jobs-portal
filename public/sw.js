// Vue3 Job Portal Service Worker
// Performance optimization with caching strategies

const CACHE_NAME = 'jobportal-v1.2.1';
const STATIC_CACHE = 'jobportal-static-v1.2.1';
const DYNAMIC_CACHE = 'jobportal-dynamic-v1.2.1';
const API_CACHE = 'jobportal-api-v1.2.1';

// Assets to cache on install (updated from latest build)
const STATIC_ASSETS = [
  '/',
  '/build/assets/main-B82xsCq1.js',
  '/build/styles/main-T-oL49QB.css',
  '/build/styles/app-DkzJi6LJ.css',
  '/build/auth/auth-chunk-CR3SY8LN.js',
  '/build/chunks/chunk-DAfKnozS.js', // vendor-vue
  '/build/chunks/chunk-DWz9A8Xh.js', // utils-chunk
  '/build/chunks/chunk-BYJP1jeu.js', // vendor-ui
  '/offline.html',
];

// API endpoints to cache with TTL
const API_CACHE_CONFIG = {
  '/api/user': { ttl: 300000 }, // 5 minutes
  '/api/jobs': { ttl: 600000 }, // 10 minutes
  '/api/companies': { ttl: 1800000 }, // 30 minutes
  '/api/categories': { ttl: 3600000 }, // 1 hour
  '/api/skills': { ttl: 3600000 }, // 1 hour
};

// Routes that should work offline
const OFFLINE_FALLBACK_ROUTES = [
  '/',
  '/login',
  '/register',
  '/jobs',
  '/companies',
  '/candidate/dashboard',
  '/employer/dashboard',
  '/admin/dashboard',
];

// Install event - cache static assets
self.addEventListener('install', (event) => {
  console.log('[SW] Installing service worker...');
  
  event.waitUntil(
    Promise.all([
      // Cache static assets
      caches.open(STATIC_CACHE).then((cache) => {
        console.log('[SW] Caching static assets');
        return cache.addAll(STATIC_ASSETS.map(url => new Request(url, {
          mode: 'cors',
          credentials: 'omit'
        })));
      }),
      
      // Create other cache stores
      caches.open(DYNAMIC_CACHE),
      caches.open(API_CACHE),
    ]).then(() => {
      console.log('[SW] All caches created successfully');
      // Skip waiting to activate immediately
      return self.skipWaiting();
    })
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  console.log('[SW] Activating service worker...');
  
  event.waitUntil(
    Promise.all([
      // Clean up old caches
      caches.keys().then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => {
            if (cacheName !== STATIC_CACHE && 
                cacheName !== DYNAMIC_CACHE && 
                cacheName !== API_CACHE &&
                cacheName !== CACHE_NAME) {
              console.log('[SW] Deleting old cache:', cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      }),
      
      // Take control of all clients
      self.clients.claim(),
    ]).then(() => {
      console.log('[SW] Service worker activated and ready');
    })
  );
});

// Fetch event - implement caching strategies
self.addEventListener('fetch', (event) => {
  const request = event.request;
  const url = new URL(request.url);
  
  // Skip non-GET requests and chrome-extension requests
  if (request.method !== 'GET' || url.protocol === 'chrome-extension:') {
    return;
  }
  
  // API requests - Cache with TTL
  if (url.pathname.startsWith('/api/')) {
    event.respondWith(handleApiRequest(request));
    return;
  }
  
  // Static assets - Cache first strategy
  if (isStaticAsset(url.pathname)) {
    event.respondWith(handleStaticAsset(request));
    return;
  }
  
  // Navigation requests - Network first with offline fallback
  if (request.mode === 'navigate') {
    event.respondWith(handleNavigation(request));
    return;
  }
  
  // Default - Network first, cache fallback
  event.respondWith(handleDefault(request));
});

// Handle API requests with smart caching
async function handleApiRequest(request) {
  const url = new URL(request.url);
  const cacheKey = url.pathname;
  const config = API_CACHE_CONFIG[cacheKey] || { ttl: 300000 }; // Default 5 min
  
  try {
    const cache = await caches.open(API_CACHE);
    
    // Check cached response
    const cachedResponse = await cache.match(request);
    
    if (cachedResponse) {
      const cachedDate = new Date(cachedResponse.headers.get('sw-cache-date'));
      const isExpired = Date.now() - cachedDate.getTime() > config.ttl;
      
      if (!isExpired) {
        console.log('[SW] Serving cached API response:', cacheKey);
        return cachedResponse;
      } else {
        console.log('[SW] Cached API response expired:', cacheKey);
        await cache.delete(request);
      }
    }
    
    // Fetch fresh data
    console.log('[SW] Fetching fresh API response:', cacheKey);
    const response = await fetch(request);
    
    if (response.ok) {
      // Clone and add cache headers
      const responseToCache = response.clone();
      const headers = new Headers(responseToCache.headers);
      headers.set('sw-cache-date', new Date().toISOString());
      
      const cachedResponse = new Response(await responseToCache.blob(), {
        status: responseToCache.status,
        statusText: responseToCache.statusText,
        headers: headers
      });
      
      // Cache the response
      await cache.put(request, cachedResponse);
      console.log('[SW] Cached API response:', cacheKey);
    }
    
    return response;
    
  } catch (error) {
    console.log('[SW] API request failed, checking cache:', error);
    
    // Return cached version if available (even if expired)
    const cache = await caches.open(API_CACHE);
    const cachedResponse = await cache.match(request);
    
    if (cachedResponse) {
      console.log('[SW] Serving stale cached API response:', cacheKey);
      return cachedResponse;
    }
    
    // Return offline API response
    return new Response(JSON.stringify({
      error: 'Offline',
      message: 'This feature is not available offline',
      cached: false
    }), {
      status: 503,
      headers: { 'Content-Type': 'application/json' }
    });
  }
}

// Handle static assets with cache-first strategy
async function handleStaticAsset(request) {
  try {
    const cache = await caches.open(STATIC_CACHE);
    const cachedResponse = await cache.match(request);
    
    if (cachedResponse) {
      console.log('[SW] Serving cached static asset');
      return cachedResponse;
    }
    
    // Fetch and cache new static asset
    const response = await fetch(request);
    if (response.ok) {
      console.log('[SW] Caching new static asset');
      await cache.put(request, response.clone());
    }
    
    return response;
    
  } catch (error) {
    console.log('[SW] Static asset request failed:', error);
    throw error;
  }
}

// Handle navigation with network-first and offline fallback
async function handleNavigation(request) {
  try {
    // Try network first
    const response = await fetch(request);
    
    // Cache successful navigation responses
    if (response.ok) {
      const cache = await caches.open(DYNAMIC_CACHE);
      await cache.put(request, response.clone());
      console.log('[SW] Cached navigation response');
    }
    
    return response;
    
  } catch (error) {
    console.log('[SW] Navigation request failed, checking cache:', error);
    
    // Check dynamic cache
    const cache = await caches.open(DYNAMIC_CACHE);
    const cachedResponse = await cache.match(request);
    
    if (cachedResponse) {
      console.log('[SW] Serving cached navigation');
      return cachedResponse;
    }
    
    // Check if route should work offline
    const url = new URL(request.url);
    const shouldWorkOffline = OFFLINE_FALLBACK_ROUTES.some(route => {
      return url.pathname === route || url.pathname.startsWith(route + '/');
    });
    
    if (shouldWorkOffline) {
      // Return the main app shell for SPA routes
      const appShellResponse = await cache.match('/');
      if (appShellResponse) {
        console.log('[SW] Serving app shell for offline route');
        return appShellResponse;
      }
    }
    
    // Return offline page
    const offlineResponse = await cache.match('/offline.html');
    if (offlineResponse) {
      console.log('[SW] Serving offline page');
      return offlineResponse;
    }
    
    // Last resort - basic offline response
    return new Response(`
      <!DOCTYPE html>
      <html>
        <head>
          <title>Offline - Job Portal</title>
          <meta charset="utf-8">
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <style>
            body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
            h1 { color: #e74c3c; }
            p { color: #7f8c8d; }
          </style>
        </head>
        <body>
          <h1>You're Offline</h1>
          <p>Please check your internet connection and try again.</p>
          <button onclick="window.location.reload()">Try Again</button>
        </body>
      </html>
    `, {
      status: 503,
      headers: { 'Content-Type': 'text/html' }
    });
  }
}

// Handle default requests
async function handleDefault(request) {
  try {
    const response = await fetch(request);
    
    // Cache successful responses
    if (response.ok) {
      const cache = await caches.open(DYNAMIC_CACHE);
      await cache.put(request, response.clone());
    }
    
    return response;
    
  } catch (error) {
    // Return cached version if available
    const cache = await caches.open(DYNAMIC_CACHE);
    const cachedResponse = await cache.match(request);
    
    if (cachedResponse) {
      console.log('[SW] Serving cached response for:', request.url);
      return cachedResponse;
    }
    
    throw error;
  }
}

// Helper function to check if URL is a static asset
function isStaticAsset(pathname) {
  return pathname.startsWith('/build/') ||
         pathname.startsWith('/assets/') ||
         pathname.startsWith('/images/') ||
         pathname.startsWith('/fonts/') ||
         pathname.endsWith('.css') ||
         pathname.endsWith('.js') ||
         pathname.endsWith('.woff2') ||
         pathname.endsWith('.woff') ||
         pathname.endsWith('.png') ||
         pathname.endsWith('.jpg') ||
         pathname.endsWith('.jpeg') ||
         pathname.endsWith('.svg') ||
         pathname.endsWith('.ico');
}

// Background sync for failed API requests
self.addEventListener('sync', (event) => {
  if (event.tag === 'background-sync') {
    console.log('[SW] Background sync triggered');
    event.waitUntil(doBackgroundSync());
  }
});

// Handle background sync
async function doBackgroundSync() {
  try {
    // Implement background sync logic here
    // e.g., retry failed API requests
    console.log('[SW] Background sync completed');
  } catch (error) {
    console.log('[SW] Background sync failed:', error);
  }
}

// Handle push notifications (future enhancement)
self.addEventListener('push', (event) => {
  if (event.data) {
    const data = event.data.json();
    console.log('[SW] Push notification received:', data);
    
    // Show notification
    event.waitUntil(
      self.registration.showNotification(data.title, {
        body: data.body,
        icon: '/icons/icon-192x192.png',
        badge: '/icons/badge-72x72.png',
        tag: data.tag || 'general',
        actions: data.actions || []
      })
    );
  }
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
  console.log('[SW] Notification clicked:', event.notification);
  
  event.notification.close();
  
  event.waitUntil(
    clients.openWindow(event.notification.data?.url || '/')
  );
});

console.log('[SW] Service worker script loaded successfully');