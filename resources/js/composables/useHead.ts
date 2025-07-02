import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

interface MetaTag {
  name?: string;
  property?: string;
  content: string;
  key?: string;
}

interface HeadConfig {
  title?: string;
  titleTemplate?: string;
  meta?: MetaTag[];
  link?: LinkTag[];
  script?: ScriptTag[];
}

interface LinkTag {
  rel: string;
  href: string;
  type?: string;
  media?: string;
  key?: string;
}

interface ScriptTag {
  src?: string;
  type?: string;
  async?: boolean;
  defer?: boolean;
  innerHTML?: string;
  key?: string;
}

class HeadManager {
  private addedElements: Set<HTMLElement> = new Set();
  private originalTitle: string = '';

  constructor() {
    this.originalTitle = document.title;
  }

  updateTitle(title: string, template?: string): void {
    if (template) {
      document.title = template.replace('%s', title);
    } else {
      document.title = title;
    }
  }

  addMetaTag(meta: MetaTag): HTMLMetaElement {
    const element = document.createElement('meta');
    
    if (meta.name) element.name = meta.name;
    if (meta.property) element.setAttribute('property', meta.property);
    element.content = meta.content;
    
    if (meta.key) element.setAttribute('data-head-key', meta.key);

    // Remove existing meta tag with same key or name/property
    this.removeExistingMeta(meta);

    document.head.appendChild(element);
    this.addedElements.add(element);
    
    return element;
  }

  addLinkTag(link: LinkTag): HTMLLinkElement {
    const element = document.createElement('link');
    
    element.rel = link.rel;
    element.href = link.href;
    if (link.type) element.type = link.type;
    if (link.media) element.media = link.media;
    if (link.key) element.setAttribute('data-head-key', link.key);

    // Remove existing link tag with same key or rel+href
    this.removeExistingLink(link);

    document.head.appendChild(element);
    this.addedElements.add(element);
    
    return element;
  }

  addScriptTag(script: ScriptTag): HTMLScriptElement {
    const element = document.createElement('script');
    
    if (script.src) element.src = script.src;
    if (script.type) element.type = script.type;
    if (script.async) element.async = script.async;
    if (script.defer) element.defer = script.defer;
    if (script.innerHTML) element.innerHTML = script.innerHTML;
    if (script.key) element.setAttribute('data-head-key', script.key);

    // Remove existing script tag with same key or src
    this.removeExistingScript(script);

    document.head.appendChild(element);
    this.addedElements.add(element);
    
    return element;
  }

  private removeExistingMeta(meta: MetaTag): void {
    const selector = meta.key 
      ? `meta[data-head-key="${meta.key}"]`
      : meta.name 
        ? `meta[name="${meta.name}"]`
        : `meta[property="${meta.property}"]`;

    const existing = document.head.querySelector(selector);
    if (existing && this.addedElements.has(existing as HTMLElement)) {
      existing.remove();
      this.addedElements.delete(existing as HTMLElement);
    }
  }

  private removeExistingLink(link: LinkTag): void {
    const selector = link.key 
      ? `link[data-head-key="${link.key}"]`
      : `link[rel="${link.rel}"][href="${link.href}"]`;

    const existing = document.head.querySelector(selector);
    if (existing && this.addedElements.has(existing as HTMLElement)) {
      existing.remove();
      this.addedElements.delete(existing as HTMLElement);
    }
  }

  private removeExistingScript(script: ScriptTag): void {
    const selector = script.key 
      ? `script[data-head-key="${script.key}"]`
      : script.src 
        ? `script[src="${script.src}"]`
        : null;

    if (selector) {
      const existing = document.head.querySelector(selector);
      if (existing && this.addedElements.has(existing as HTMLElement)) {
        existing.remove();
        this.addedElements.delete(existing as HTMLElement);
      }
    }
  }

  cleanup(): void {
    // Remove all added elements
    this.addedElements.forEach(element => {
      if (element.parentNode) {
        element.parentNode.removeChild(element);
      }
    });
    this.addedElements.clear();

    // Restore original title
    document.title = this.originalTitle;
  }

  clear(): void {
    this.cleanup();
  }
}

const globalHeadManager = new HeadManager();

export function useHead(config: HeadConfig | (() => HeadConfig)) {
  const cleanupFunctions: (() => void)[] = [];
  
  const updateHead = () => {
    // Clear previous updates
    cleanupFunctions.forEach(fn => fn());
    cleanupFunctions.length = 0;

    const currentConfig = typeof config === 'function' ? config() : config;

    // Update title
    if (currentConfig.title) {
      globalHeadManager.updateTitle(currentConfig.title, currentConfig.titleTemplate);
    }

    // Add meta tags
    if (currentConfig.meta) {
      currentConfig.meta.forEach(meta => {
        globalHeadManager.addMetaTag(meta);
      });
    }

    // Add link tags
    if (currentConfig.link) {
      currentConfig.link.forEach(link => {
        globalHeadManager.addLinkTag(link);
      });
    }

    // Add script tags
    if (currentConfig.script) {
      currentConfig.script.forEach(script => {
        globalHeadManager.addScriptTag(script);
      });
    }
  };

  onMounted(() => {
    updateHead();

    // Watch for changes if config is reactive
    if (typeof config === 'function') {
      const stopWatcher = watch(config, updateHead, { deep: true });
      cleanupFunctions.push(stopWatcher);
    }
  });

  onUnmounted(() => {
    cleanupFunctions.forEach(fn => fn());
    // Note: We don't call globalHeadManager.cleanup() here because
    // other components might be using it
  });

  return {
    updateHead
  };
}

// Helper function for creating structured data
export function useStructuredData(data: object) {
  const scriptKey = `structured-data-${Math.random().toString(36).substr(2, 9)}`;
  
  return useHead({
    script: [
      {
        key: scriptKey,
        type: 'application/ld+json',
        innerHTML: JSON.stringify(data)
      }
    ]
  });
}

// Helper function for Open Graph tags
export function useOpenGraph(og: {
  title?: string;
  description?: string;
  image?: string;
  url?: string;
  type?: string;
  siteName?: string;
}) {
  const meta: MetaTag[] = [];
  
  if (og.title) meta.push({ property: 'og:title', content: og.title });
  if (og.description) meta.push({ property: 'og:description', content: og.description });
  if (og.image) meta.push({ property: 'og:image', content: og.image });
  if (og.url) meta.push({ property: 'og:url', content: og.url });
  if (og.type) meta.push({ property: 'og:type', content: og.type });
  if (og.siteName) meta.push({ property: 'og:site_name', content: og.siteName });

  return useHead({ meta });
}

// Helper function for Twitter Card tags
export function useTwitterCard(twitter: {
  card?: string;
  site?: string;
  creator?: string;
  title?: string;
  description?: string;
  image?: string;
}) {
  const meta: MetaTag[] = [];
  
  if (twitter.card) meta.push({ name: 'twitter:card', content: twitter.card });
  if (twitter.site) meta.push({ name: 'twitter:site', content: twitter.site });
  if (twitter.creator) meta.push({ name: 'twitter:creator', content: twitter.creator });
  if (twitter.title) meta.push({ name: 'twitter:title', content: twitter.title });
  if (twitter.description) meta.push({ name: 'twitter:description', content: twitter.description });
  if (twitter.image) meta.push({ name: 'twitter:image', content: twitter.image });

  return useHead({ meta });
}

export { globalHeadManager }; 