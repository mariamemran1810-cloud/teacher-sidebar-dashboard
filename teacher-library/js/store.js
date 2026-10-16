/* =========================================================
   طبقة الحفظ المحلي (localStorage) + تصدير/استيراد JSON
   ========================================================= */

const MTLStore = (function(){
  const K_SETTINGS = 'mtl:v1:settings';
  const K_CONTENT  = 'mtl:v1:content';
  const K_VERSION  = 'mtl:v1:version';

  function safeParse(raw, fallback){
    try{ const v = JSON.parse(raw); return (v && typeof v === 'object') ? v : fallback; }
    catch(e){ return fallback; }
  }

  function getSettings(){
    const saved = safeParse(localStorage.getItem(K_SETTINGS), {});
    return Object.assign({}, MTL_DEFAULT_SETTINGS, saved);
  }

  function saveSettings(settings){
    try{
      localStorage.setItem(K_SETTINGS, JSON.stringify(settings));
      return true;
    }catch(e){ console.error('تعذّر حفظ الإعدادات', e); return false; }
  }

  function getContent(){
    return safeParse(localStorage.getItem(K_CONTENT), {});
  }

  function setValue(key, value){
    if(!key) return false;
    try{
      const all = getContent();
      all[key] = value;
      localStorage.setItem(K_CONTENT, JSON.stringify(all));
      return true;
    }catch(e){
      console.error('تعذّر الحفظ', e);
      return false;
    }
  }

  /* حفظ دفعة واحدة (توفير الكتابة المتكررة) */
  function setValues(map){
    try{
      const all = getContent();
      Object.assign(all, map);
      localStorage.setItem(K_CONTENT, JSON.stringify(all));
      return true;
    }catch(e){ console.error('تعذّر الحفظ', e); return false; }
  }

  function clearAll(){
    try{
      localStorage.removeItem(K_SETTINGS);
      localStorage.removeItem(K_CONTENT);
      localStorage.removeItem(K_VERSION);
      return true;
    }catch(e){ return false; }
  }

  function exportPayload(){
    return {
      app:'مكتبتي التعليمية للمعلمة الليبية',
      version:1,
      exportedAt:new Date().toISOString(),
      settings:getSettings(),
      content:getContent(),
      note:'بيانات شخصية محفوظة محليًا في المتصفح — ليست نماذج معتمدة رسميًا.'
    };
  }

  function importPayload(obj){
    if(!obj || typeof obj !== 'object') throw new Error('ملف غير صالح');
    if(obj.settings) saveSettings(Object.assign({}, MTL_DEFAULT_SETTINGS, obj.settings));
    if(obj.content && typeof obj.content === 'object'){
      localStorage.setItem(K_CONTENT, JSON.stringify(obj.content));
    }
    return true;
  }

  function usage(){
    let bytes = 0;
    try{
      bytes = ((localStorage.getItem(K_SETTINGS) || '').length +
               (localStorage.getItem(K_CONTENT)  || '').length) * 2; // تقريبي (UTF-16)
    }catch(e){}
    const kb = (bytes / 1024).toFixed(1);
    const fields = Object.keys(getContent()).length;
    return { bytes, kb, fields };
  }

  return { getSettings, saveSettings, getContent, setValue, setValues,
           clearAll, exportPayload, importPayload, usage };
})();
