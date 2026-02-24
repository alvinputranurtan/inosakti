    <script>
    (() => {
      const sectionSelect = document.getElementById('editSectionSelect');
      if (!sectionSelect) return;
      sectionSelect.addEventListener('change', () => {
        const url = new URL(window.location.href);
        url.searchParams.set('edit_section', sectionSelect.value || 'metadata');
        <?php if ((int) ($selectedCourseId ?? 0) > 0): ?>
        url.searchParams.set('course_id', <?= (int) $selectedCourseId ?>);
        url.searchParams.delete('mode');
        <?php else: ?>
        url.searchParams.set('mode', 'create');
        <?php endif; ?>
        window.location.href = url.toString();
      });
    })();
    (() => {
      const openButton = document.getElementById('deleteChapterButton');
      const modal = document.getElementById('deleteChapterModal');
      const cancelButton = document.getElementById('cancelDeleteChapterButton');
      const message = document.getElementById('deleteChapterMessage');
      if (!openButton || !modal || !cancelButton || !message) return;

      openButton.addEventListener('click', () => {
        const title = (openButton.getAttribute('data-chapter-title') || '').trim();
        const suffix = title !== '' ? (' "' + title + '"') : '';
        message.textContent = 'apakah benar anda ingin menghapus chapter' + suffix + '?';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      });

      cancelButton.addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      });

      modal.addEventListener('click', (event) => {
        if (event.target === modal) {
          modal.classList.add('hidden');
          modal.classList.remove('flex');
        }
      });
    })();
    (() => {
      const openButton = document.getElementById('deleteModuleButton');
      const modal = document.getElementById('deleteModuleModal');
      const cancelButton = document.getElementById('cancelDeleteModuleButton');
      const message = document.getElementById('deleteModuleMessage');
      if (!openButton || !modal || !cancelButton || !message) return;

      openButton.addEventListener('click', () => {
        const title = (openButton.getAttribute('data-module-title') || '').trim();
        const suffix = title !== '' ? (' "' + title + '"') : '';
        message.textContent = 'apakah benar anda ingin menghapus modul' + suffix + '?';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      });

      cancelButton.addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      });

      modal.addEventListener('click', (event) => {
        if (event.target === modal) {
          modal.classList.add('hidden');
          modal.classList.remove('flex');
        }
      });
    })();
    (() => {
      const kindSelect = document.getElementById('moduleKindSelect');
      const kindBtn = document.getElementById('moduleKindChangeBtn');
      if (!kindSelect || !kindBtn) return;
      const initial = String(kindSelect.getAttribute('data-initial-kind') || '');
      const sync = () => {
        const dirty = String(kindSelect.value || '') !== initial;
        kindBtn.classList.toggle('hidden', !dirty);
      };
      kindSelect.addEventListener('change', sync);
      sync();
    })();
    (() => {
      const editorEl = document.getElementById('moduleArticleEditor');
      if (!editorEl) return;
      const initEditor = () => {
        if (typeof window.tinymce === 'undefined') return;
        window.tinymce.remove('#moduleArticleEditor');
        window.tinymce.init({
          selector: '#moduleArticleEditor',
          menubar: false,
          branding: false,
          height: 380,
          plugins: 'autoresize link image media table lists code fullscreen preview searchreplace visualblocks charmap',
          toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough subscript superscript | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table | link image media | removeformat | code fullscreen preview',
          image_title: true,
          automatic_uploads: true,
          file_picker_types: 'image',
          file_picker_callback: (cb, value, meta) => {
            if (meta.filetype !== 'image') return;
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = () => {
              const file = input.files && input.files[0] ? input.files[0] : null;
              if (!file) return;
              const reader = new FileReader();
              reader.onload = () => {
                cb(String(reader.result || ''), { title: file.name || 'image' });
              };
              reader.readAsDataURL(file);
            };
            input.click();
          }
        });
      };
      if (typeof window.tinymce !== 'undefined') {
        initEditor();
        return;
      }
      const script = document.createElement('script');
      script.src = <?= json_encode(admin_url('/assets/vendor/tinymce/tinymce.min.js')) ?>;
      script.onload = initEditor;
      document.head.appendChild(script);
    })();
    </script>
