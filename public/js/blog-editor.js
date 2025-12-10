/**
 * TinyMCE WYSIWYG Editor Integration for Blog Module
 */
(function() {
    'use strict';

    // Check if TinyMCE is loaded
    if (typeof tinymce === 'undefined') {
        console.warn('TinyMCE not loaded. Loading from CDN...');
        loadTinyMCE();
        return;
    }

    initTinyMCE();

    function loadTinyMCE() {
        const script = document.createElement('script');
        script.src = 'https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js';
        script.referrerPolicy = 'origin';
        script.onload = initTinyMCE;
        document.head.appendChild(script);
    }

    function initTinyMCE() {
        const editorElements = document.querySelectorAll('.wysiwyg, #content');
        
        if (editorElements.length === 0) return;

        tinymce.init({
            selector: '.wysiwyg, #content',
            height: 500,
            menubar: true,
            language: 'fr_FR',
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount', 'codesample'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'link image media codesample | removeformat | fullscreen | help',
            content_style: `
                body { 
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; 
                    font-size: 16px;
                    line-height: 1.6;
                    color: #1f2937;
                    max-width: 800px;
                    margin: 0 auto;
                    padding: 1rem;
                }
                h1 { font-size: 2rem; margin: 1rem 0 0.5rem; }
                h2 { font-size: 1.5rem; margin: 1rem 0 0.5rem; }
                h3 { font-size: 1.25rem; margin: 1rem 0 0.5rem; }
                p { margin-bottom: 1rem; }
                a { color: #4f46e5; }
                blockquote { 
                    border-left: 4px solid #4f46e5; 
                    padding-left: 1rem; 
                    margin: 1rem 0;
                    font-style: italic;
                    color: #6b7280;
                }
                pre { 
                    background: #1f2937; 
                    color: #e5e7eb; 
                    padding: 1rem; 
                    border-radius: 8px; 
                    overflow-x: auto;
                }
                code { 
                    background: #f3f4f6; 
                    padding: 0.125rem 0.25rem; 
                    border-radius: 4px;
                    font-size: 0.875em;
                }
                img { max-width: 100%; height: auto; border-radius: 8px; }
                table { border-collapse: collapse; width: 100%; }
                td, th { border: 1px solid #e5e7eb; padding: 0.5rem; }
            `,
            // Image upload configuration
            images_upload_handler: function(blobInfo, progress) {
                return new Promise((resolve, reject) => {
                    const formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    
                    fetch('/admin/blog/upload-media', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            resolve(result.url);
                        } else {
                            reject({ message: result.message || 'Upload failed' });
                        }
                    })
                    .catch(error => {
                        reject({ message: error.message });
                    });
                });
            },
            // Auto-save draft
            autosave_interval: '30s',
            autosave_prefix: 'pilom-blog-draft-{path}',
            autosave_restore_when_empty: true,
            // SEO helpers
            setup: function(editor) {
                // Character count for SEO
                editor.on('input', function() {
                    updateContentAnalysis(editor);
                });
                
                // Heading structure check
                editor.on('NodeChange', function() {
                    checkHeadingStructure(editor);
                });
            },
            // Link options
            link_default_target: '_blank',
            link_assume_external_targets: true,
            // Table options
            table_default_styles: {
                'border-collapse': 'collapse',
                'width': '100%'
            },
            // Paste as plain text by default for cleaner content
            paste_as_text: false,
            paste_merge_formats: true,
            paste_webkit_styles: 'none',
            // Performance
            cache_suffix: '?v=6',
        });
    }

    function updateContentAnalysis(editor) {
        const content = editor.getContent({ format: 'text' });
        const wordCount = content.split(/\s+/).filter(w => w.length > 0).length;
        
        // Update word count display if exists
        const wordCountEl = document.getElementById('word-count');
        if (wordCountEl) {
            wordCountEl.textContent = wordCount + ' mots';
        }
        
        // Estimate reading time
        const readingTime = Math.max(1, Math.ceil(wordCount / 200));
        const readingTimeEl = document.getElementById('reading-time');
        if (readingTimeEl) {
            readingTimeEl.textContent = readingTime + ' min de lecture';
        }
    }

    function checkHeadingStructure(editor) {
        const content = editor.getContent();
        const parser = new DOMParser();
        const doc = parser.parseFromString(content, 'text/html');
        
        const headings = doc.querySelectorAll('h1, h2, h3, h4, h5, h6');
        const warningEl = document.getElementById('heading-warning');
        
        if (!warningEl) return;
        
        // Check for H1 in content (should not be present, title is H1)
        const h1Count = doc.querySelectorAll('h1').length;
        
        if (h1Count > 0) {
            warningEl.textContent = '⚠️ Évitez les H1 dans le contenu, utilisez H2 et H3';
            warningEl.style.display = 'block';
        } else {
            warningEl.style.display = 'none';
        }
    }

    // Export for external use
    window.PilomBlogEditor = {
        init: initTinyMCE,
        updateAnalysis: updateContentAnalysis
    };
})();
