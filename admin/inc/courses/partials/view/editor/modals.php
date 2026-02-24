        <?php if ($isChapter && (int) ($selectedChapter['id'] ?? 0) > 0): ?>
          <div id="deleteChapterModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 p-4">
            <div class="w-full max-w-md rounded-2xl bg-white border border-slate-200 shadow-xl p-5">
              <h4 class="text-base font-bold text-slate-900">Konfirmasi Hapus Chapter</h4>
              <p id="deleteChapterMessage" class="mt-2 text-sm text-slate-600">apakah benar anda ingin menghapus chapter ini?</p>
              <form method="post" class="mt-4 flex gap-2 justify-end">
                <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
                <input type="hidden" name="action" value="delete_chapter_basic">
                <input type="hidden" name="course_id" value="<?= (int) ($editingCourse['id'] ?? 0) ?>">
                <input type="hidden" name="chapter_id" value="<?= (int) ($selectedChapter['id'] ?? 0) ?>">
                <button type="button" id="cancelDeleteChapterButton" class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                <button class="px-4 py-2 rounded-lg bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700">Ya, Hapus</button>
              </form>
            </div>
          </div>
        <?php endif; ?>
        <?php if ($isModule && (int) ($selectedModuleLesson['id'] ?? 0) > 0): ?>
          <div id="deleteModuleModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 p-4">
            <div class="w-full max-w-md rounded-2xl bg-white border border-slate-200 shadow-xl p-5">
              <h4 class="text-base font-bold text-slate-900">Konfirmasi Hapus Modul</h4>
              <p id="deleteModuleMessage" class="mt-2 text-sm text-slate-600">apakah benar anda ingin menghapus modul ini?</p>
              <form method="post" class="mt-4 flex gap-2 justify-end">
                <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
                <input type="hidden" name="action" value="delete_module_basic">
                <input type="hidden" name="course_id" value="<?= (int) ($editingCourse['id'] ?? 0) ?>">
                <input type="hidden" name="module_lesson_id" value="<?= (int) ($selectedModuleLesson['id'] ?? 0) ?>">
                <button type="button" id="cancelDeleteModuleButton" class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                <button class="px-4 py-2 rounded-lg bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700">Ya, Hapus</button>
              </form>
            </div>
          </div>
        <?php endif; ?>
