          <div class="lg:col-span-2 flex gap-2">
            <button class="px-4 py-2 rounded-lg bg-blue-800 text-white text-sm font-semibold"><?= $isChapter ? 'Simpan Chapter' : ($isModule ? 'Simpan Modul' : 'Simpan Kursus') ?></button>
            <?php if ($isChapter && (int) ($selectedChapter['id'] ?? 0) > 0): ?>
              <button
                type="button"
                id="deleteChapterButton"
                class="px-4 py-2 rounded-lg bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700"
                data-chapter-title="<?= admin_e((string) ($selectedChapter['title'] ?? '')) ?>">
                Hapus Chapter
              </button>
            <?php endif; ?>
            <?php if ($isModule && (int) ($selectedModuleLesson['id'] ?? 0) > 0): ?>
              <button
                type="button"
                id="deleteModuleButton"
                class="px-4 py-2 rounded-lg bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700"
                data-module-title="<?= admin_e((string) ($selectedModuleLesson['title'] ?? '')) ?>">
                Hapus Modul
              </button>
            <?php endif; ?>
            <a href="<?= admin_e(admin_url('/admin/courses')) ?>" class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-white">Batal</a>
          </div>
