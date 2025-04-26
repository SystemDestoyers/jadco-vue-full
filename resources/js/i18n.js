/**
 * Simple i18n helper for JADCO Admin
 */

// Default language
let currentLanguage = 'en';

// Translations
const translations = {
  en: {
    // Dashboard
    dashboard: 'Dashboard',
    pages: 'Pages',
    sections: 'Sections',
    messages: 'Messages',
    media: 'Media Library',
    settings: 'Settings',
    
    // Common actions
    save: 'Save Changes',
    cancel: 'Cancel',
    edit: 'Edit',
    delete: 'Delete',
    create: 'Create',
    search: 'Search',
    view: 'View',
    viewSite: 'View Site',
    logout: 'Logout',
    saving: 'Saving...',
    
    // Navigation
    content: 'CONTENT',
    communication: 'COMMUNICATION',
    system: 'SYSTEM',
    
    // Settings
    languageSettings: 'Language Settings',
    displaySettings: 'Display Settings',
    chooseLanguage: 'Choose your preferred display language for the admin interface',
    colorMode: 'Color Mode',
    light: 'Light',
    dark: 'Dark',
    auto: 'Auto (System Default)',
    textDirection: 'Text Direction',
    rtl: 'Right to Left',
    ltr: 'Left to Right',
    
    // Success messages
    settingsSaved: 'Settings saved successfully',
    
    // Error messages
    settingsError: 'Error saving settings'
  },
  
  ar: {
    // Dashboard
    dashboard: 'لوحة التحكم',
    pages: 'الصفحات',
    sections: 'الأقسام',
    messages: 'الرسائل',
    media: 'مكتبة الوسائط',
    settings: 'الإعدادات',
    
    // Common actions
    save: 'حفظ التغييرات',
    cancel: 'إلغاء',
    edit: 'تعديل',
    delete: 'حذف',
    create: 'إنشاء',
    search: 'بحث',
    view: 'عرض',
    viewSite: 'عرض الموقع',
    logout: 'تسجيل الخروج',
    saving: 'جاري الحفظ...',
    
    // Navigation
    content: 'المحتوى',
    communication: 'التواصل',
    system: 'النظام',
    
    // Settings
    languageSettings: 'إعدادات اللغة',
    displaySettings: 'إعدادات العرض',
    chooseLanguage: 'اختر لغة العرض المفضلة لواجهة المشرف',
    colorMode: 'وضع الألوان',
    light: 'فاتح',
    dark: 'داكن',
    auto: 'تلقائي (حسب إعدادات النظام)',
    textDirection: 'اتجاه النص',
    rtl: 'من اليمين إلى اليسار',
    ltr: 'من اليسار إلى اليمين',
    
    // Success messages
    settingsSaved: 'تم حفظ الإعدادات بنجاح',
    
    // Error messages
    settingsError: 'حدث خطأ أثناء حفظ الإعدادات'
  }
};

// Initialize from localStorage if available
if (typeof window !== 'undefined' && localStorage) {
  const savedLanguage = localStorage.getItem('admin_language');
  if (savedLanguage && translations[savedLanguage]) {
    currentLanguage = savedLanguage;
    document.documentElement.lang = currentLanguage;
    document.documentElement.setAttribute('data-language', currentLanguage);
    
    // Set text direction
    if (currentLanguage === 'ar') {
      document.documentElement.dir = 'rtl';
    } else {
      document.documentElement.dir = 'ltr';
    }
  }
}

/**
 * Translate a key
 * @param {string} key - The translation key
 * @param {Object} params - Optional parameters for interpolation
 * @returns {string} Translated text
 */
export function t(key, params = {}) {
  // Get the current language's translations or fallback to English
  const langTranslations = translations[currentLanguage] || translations.en;
  
  // Get the translation or fallback to the key itself
  let translation = langTranslations[key] || translations.en[key] || key;
  
  // Handle parameter interpolation
  if (params && Object.keys(params).length) {
    Object.keys(params).forEach(param => {
      translation = translation.replace(`{${param}}`, params[param]);
    });
  }
  
  return translation;
}

/**
 * Change the current language
 * @param {string} lang - The language code
 */
export function setLanguage(lang) {
  if (translations[lang]) {
    currentLanguage = lang;
    
    // Save to localStorage
    if (typeof window !== 'undefined' && localStorage) {
      localStorage.setItem('admin_language', lang);
    }
    
    // Update document attributes
    document.documentElement.lang = lang;
    document.documentElement.setAttribute('data-language', lang);
    
    // Set text direction
    if (lang === 'ar') {
      document.documentElement.dir = 'rtl';
    } else {
      document.documentElement.dir = 'ltr';
    }
    
    // Trigger a custom event that components can listen to
    if (typeof window !== 'undefined') {
      window.dispatchEvent(new CustomEvent('languageChanged', { detail: { language: lang } }));
    }
  } else {
    console.warn(`Language "${lang}" is not supported.`);
  }
}

/**
 * Get the current language
 * @returns {string} Current language code
 */
export function getCurrentLanguage() {
  return currentLanguage;
}

/**
 * Check if the current language is RTL
 * @returns {boolean} True if the current language is RTL
 */
export function isRTL() {
  return currentLanguage === 'ar';
}

/**
 * Set text direction manually
 * @param {string} direction - The direction ('rtl' or 'ltr')
 */
export function setDirection(direction) {
  if (direction === 'rtl' || direction === 'ltr') {
    document.documentElement.dir = direction;
    
    // Trigger a custom event that components can listen to
    if (typeof window !== 'undefined') {
      window.dispatchEvent(new CustomEvent('directionChanged', { detail: { direction } }));
    }
  } else {
    console.warn(`Direction "${direction}" is not valid. Use 'rtl' or 'ltr'.`);
  }
}

export default {
  t,
  setLanguage,
  getCurrentLanguage,
  isRTL,
  setDirection
}; 