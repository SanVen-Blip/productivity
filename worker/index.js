/**
 * SanvenDocs Worker — Reverse proxy to Cloudflare Quick Tunnel.
 * 
 * URL tetap: https://sanvendocs.workers.dev
 * 
 * Update TUNNEL_URL setiap kali restart Quick Tunnel.
 * Atau gunakan KV binding untuk update tanpa redeploy.
 */

const TUNNEL_URL = 'https://proved-inflation-juvenile-club.trycloudflare.com';

export default {
  async fetch(request) {
    const url = new URL(request.url);
    const targetUrl = TUNNEL_URL + url.pathname + url.search;

    // Clone request and forward to tunnel
    const modifiedRequest = new Request(targetUrl, {
      method: request.method,
      headers: request.headers,
      body: request.body,
      redirect: 'follow',
    });

    // Forward host header
    modifiedRequest.headers.set('X-Forwarded-Host', url.hostname);

    try {
      const response = await fetch(modifiedRequest);
      
      // Return response with CORS headers
      const newResponse = new Response(response.body, response);
      newResponse.headers.set('X-Proxied-By', 'sanvendocs-worker');
      return newResponse;
    } catch (err) {
      return new Response(
        '<html><body style="font-family:sans-serif;text-align:center;padding:60px">' +
        '<h1>SanvenDocs</h1>' +
        '<p>Server sedang offline. Coba lagi nanti.</p>' +
        '<p style="color:#999;font-size:12px">Error: ' + err.message + '</p>' +
        '</body></html>',
        { status: 502, headers: { 'Content-Type': 'text/html' } }
      );
    }
  },
};
