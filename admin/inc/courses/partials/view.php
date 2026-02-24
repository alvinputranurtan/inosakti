<div class="space-y-6">
  <?php if ($isCreateMode || $editingCourse): ?>
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
      <div class="p-5 border-b border-slate-200">
        <h3 class="font-bold text-base"><?= $editingCourse ? 'Edit Kursus (Dasar)' : 'Tambah Kursus (Dasar)' ?></h3>
        <p class="text-xs text-slate-500 mt-1">Form dasar dulu: judul, slug, harga, status.</p>
      </div>
      <div class="p-5 bg-slate-50">
        <form method="post" enctype="multipart/form-data" class="grid lg:grid-cols-2 gap-3">
          <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
          <input type="hidden" name="id" value="<?= (int) ($editingCourse['id'] ?? 0) ?>">
          <input type="hidden" name="course_id" value="<?= (int) ($editingCourse['id'] ?? 0) ?>">
          <div class="lg:col-span-2">
            <label class="block text-xs font-semibold mb-1">Bagian Yang Diedit</label>
            <select id="editSectionSelect" name="edit_section" class="w-full rounded-lg border-slate-300">
              <?php foreach ($editSections as $sec): ?>
                <option value="<?= admin_e((string) ($sec['value'] ?? '')) ?>" <?= ((string) ($sec['value'] ?? '') === $selectedEditSection) ? 'selected' : '' ?>>
                  <?= admin_e((string) ($sec['label'] ?? '')) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php $selectedStatus = (string) ($editingCourse['status'] ?? 'draft'); ?>
          <?php $savedGroupLabel = trim((string) ($editingCourse['level_group_label'] ?? '')); ?>
          <?php $selectedLevel = (string) ($editingCourse['level'] ?? 'beginner'); ?>
          <?php if ($selectedLevel === 'group'): ?>
            <?php $selectedLevel = 'custom'; ?>
          <?php endif; ?>
          <?php $isChapter = strpos($selectedEditSection, 'chapter:') === 0; ?>
          <?php $isModule = strpos($selectedEditSection, 'module:') === 0; ?>
          <?php $isFrontCard = $selectedEditSection === 'front-card'; ?>
          <?php $isLanding = $selectedEditSection === 'landing'; ?>
          <?php if ($isChapter): ?>
            <input type="hidden" name="action" value="save_chapter_basic">
            <input type="hidden" name="chapter_id" value="<?= (int) ($selectedChapter['id'] ?? 0) ?>">
            <div>
              <label class="block text-xs font-semibold mb-1">Nama Chapter</label>
              <input type="text" name="chapter_title" class="w-full rounded-lg border-slate-300" value="<?= admin_e((string) ($selectedChapter['title'] ?? '')) ?>" required>
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">Nomor Urut Chapter</label>
              <input type="number" min="1" name="chapter_order" class="w-full rounded-lg border-slate-300" value="<?= (int) ($selectedChapter['module_order'] ?? 1) ?>" required>
            </div>
          <?php elseif ($isModule): ?>
            <?php
              $moduleLessonType = (string) ($selectedModuleLesson['lesson_type'] ?? '');
              $moduleLessonVariant = (string) ($selectedModuleLesson['lesson_variant'] ?? '');
              $moduleDurationMinutes = max(0, (int) round(((int) ($selectedModuleLesson['duration_seconds'] ?? 0)) / 60));
              $moduleKind = 'article';
              if ($moduleLessonType === 'video') {
                  $moduleKind = 'video';
              } elseif ($moduleLessonType === 'presentation') {
                  $moduleKind = 'powerpoint';
              } elseif ($moduleLessonType === 'quiz') {
                  $moduleKind = in_array($moduleLessonVariant, ['quiz_multiple_choice', 'quiz_essay', 'quiz_submit_file'], true)
                      ? $moduleLessonVariant
                      : 'quiz_multiple_choice';
              } elseif ($moduleLessonType === 'test') {
                  $moduleKind = in_array($moduleLessonVariant, ['test_multiple_choice', 'test_essay', 'test_submit_file'], true)
                      ? $moduleLessonVariant
                      : 'test_multiple_choice';
              } elseif ($moduleLessonVariant !== '' && in_array($moduleLessonVariant, ['article', 'video'], true)) {
                  $moduleKind = $moduleLessonVariant;
              }
            ?>
            <input type="hidden" name="action" value="save_module_basic">
            <input type="hidden" name="module_lesson_id" value="<?= (int) ($selectedModuleLesson['id'] ?? 0) ?>">
            <div class="lg:col-span-2">
              <label class="block text-xs font-semibold mb-1">Nama Modul</label>
              <input type="text" name="module_title" class="w-full rounded-lg border-slate-300" value="<?= admin_e((string) ($selectedModuleLesson['title'] ?? '')) ?>" required>
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">Nomor Urut Modul</label>
              <input type="number" min="1" step="1" name="module_lesson_order" class="w-full rounded-lg border-slate-300" value="<?= (int) ($selectedModuleLesson['lesson_order'] ?? 1) ?>" required>
            </div>
            <div class="lg:col-span-2">
              <label class="block text-xs font-semibold mb-1">Jenis Modul</label>
              <div class="flex flex-col sm:flex-row gap-2">
                <select id="moduleKindSelect" name="module_kind" class="w-full rounded-lg border-slate-300" data-initial-kind="<?= admin_e($moduleKind) ?>">
                  <option value="article" <?= $moduleKind === 'article' ? 'selected' : '' ?>>Article</option>
                  <option value="video" <?= $moduleKind === 'video' ? 'selected' : '' ?>>Video</option>
                  <option value="powerpoint" <?= $moduleKind === 'powerpoint' ? 'selected' : '' ?>>Power Point</option>
                  <option value="quiz_multiple_choice" <?= $moduleKind === 'quiz_multiple_choice' ? 'selected' : '' ?>>Quiz Multiple Choice</option>
                  <option value="quiz_essay" <?= $moduleKind === 'quiz_essay' ? 'selected' : '' ?>>Quiz Essay</option>
                  <option value="quiz_submit_file" <?= $moduleKind === 'quiz_submit_file' ? 'selected' : '' ?>>Quiz Submit File</option>
                  <option value="test_multiple_choice" <?= $moduleKind === 'test_multiple_choice' ? 'selected' : '' ?>>Test Multiple Choice</option>
                  <option value="test_essay" <?= $moduleKind === 'test_essay' ? 'selected' : '' ?>>Test Essay</option>
                  <option value="test_submit_file" <?= $moduleKind === 'test_submit_file' ? 'selected' : '' ?>>Test Submit File</option>
                </select>
                <button type="submit" id="moduleKindChangeBtn" class="hidden px-4 py-2 rounded-lg bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600 whitespace-nowrap">Ubah Jenis Modul</button>
              </div>
              <p class="mt-1 text-xs text-slate-500">Bagian isi detail modul akan kita lengkapi setelah ini.</p>
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">Durasi (menit)</label>
              <input type="number" min="0" step="1" name="module_duration_minutes" class="w-full rounded-lg border-slate-300" value="<?= $moduleDurationMinutes ?>">
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">Akses</label>
              <label class="inline-flex items-center gap-2 text-sm text-slate-700 mt-2">
                <input type="checkbox" name="module_is_preview" value="1" class="rounded border-slate-300 text-blue-700" <?= ((int) ($selectedModuleLesson['is_preview'] ?? 0) === 1) ? 'checked' : '' ?>>
                <span>Buka sebagai preview</span>
              </label>
            </div>
            <?php if ($moduleKind === 'article'): ?>
              <div class="lg:col-span-2">
                <label class="block text-xs font-semibold mb-1">Konten Article</label>
                <textarea id="moduleArticleEditor" name="module_article_html" rows="12" class="w-full rounded-lg border-slate-300"><?= admin_e((string) ($selectedModuleLesson['content_body'] ?? '')) ?></textarea>
              </div>
            <?php elseif ($moduleKind === 'video'): ?>
              <div class="lg:col-span-2">
                <label class="block text-xs font-semibold mb-1">Teks di Atas Video</label>
                <textarea name="module_video_intro_text" rows="4" class="w-full rounded-lg border-slate-300"><?= admin_e((string) ($selectedModuleLesson['content_body'] ?? '')) ?></textarea>
              </div>
              <div class="lg:col-span-2">
                <label class="block text-xs font-semibold mb-1">Link Video</label>
                <input type="text" name="module_video_url" class="w-full rounded-lg border-slate-300" value="<?= admin_e((string) ($selectedModuleLesson['content_url'] ?? '')) ?>" placeholder="/assets/uploads/courses/videos/video.mp4 atau URL video langsung">
              </div>
              <div class="lg:col-span-2">
                <label class="block text-xs font-semibold mb-1">Atau Upload Video</label>
                <input type="file" name="module_video_file" accept="video/mp4,video/webm,video/ogg,.mp4,.webm,.ogg" class="w-full rounded-lg border-slate-300">
                <p class="mt-1 text-xs text-slate-500">Format yang didukung: mp4, webm, ogg.</p>
              </div>
              <div class="lg:col-span-2">
                <label class="block text-xs font-semibold mb-1">Preview Video</label>
                <?php $previewVideoUrl = trim((string) ($selectedModuleLesson['content_url'] ?? '')); ?>
                <?php if ($previewVideoUrl !== ''): ?>
                  <video controls class="w-full rounded-lg border border-slate-200 bg-black">
                    <source src="<?= admin_e($previewVideoUrl) ?>">
                    Browser tidak mendukung preview video ini.
                  </video>
                <?php else: ?>
                  <div class="rounded-lg border border-slate-200 bg-slate-100 text-slate-500 text-sm p-4">Belum ada video untuk dipreview.</div>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          <?php elseif ($isLanding): ?>
            <input type="hidden" name="action" value="save_course_basic">
            <div class="lg:col-span-2">
              <label class="block text-xs font-semibold mb-1">Deskripsi Landing Page</label>
              <textarea name="landing_description" rows="4" class="w-full rounded-lg border-slate-300" placeholder="Deskripsi utama landing page kursus..."><?= admin_e((string) ($landingConfig['description'] ?? '')) ?></textarea>
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">Warna Border Hero Image</label>
              <?php $heroBorderPreset = (string) ($landingConfig['hero_border_preset'] ?? 'border-white'); ?>
              <select name="hero_border_preset" class="w-full rounded-lg border-slate-300">
                <option value="border-white" <?= $heroBorderPreset === 'border-white' ? 'selected' : '' ?>>Putih</option>
                <option value="border-cyan" <?= $heroBorderPreset === 'border-cyan' ? 'selected' : '' ?>>Cyan</option>
                <option value="border-amber" <?= $heroBorderPreset === 'border-amber' ? 'selected' : '' ?>>Amber</option>
                <option value="border-emerald" <?= $heroBorderPreset === 'border-emerald' ? 'selected' : '' ?>>Emerald</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">Warna Background Hero Section</label>
              <?php $heroBgPreset = (string) ($landingConfig['hero_bg_preset'] ?? 'slate-cyan'); ?>
              <select name="hero_bg_preset" class="w-full rounded-lg border-slate-300">
                <option value="slate-cyan" <?= $heroBgPreset === 'slate-cyan' ? 'selected' : '' ?>>Slate + Cyan</option>
                <option value="indigo-blue" <?= $heroBgPreset === 'indigo-blue' ? 'selected' : '' ?>>Indigo + Blue</option>
                <option value="emerald-teal" <?= $heroBgPreset === 'emerald-teal' ? 'selected' : '' ?>>Emerald + Teal</option>
                <option value="amber-rose" <?= $heroBgPreset === 'amber-rose' ? 'selected' : '' ?>>Amber + Rose</option>
              </select>
            </div>
            <input type="hidden" name="title" value="<?= admin_e((string) ($editingCourse['title'] ?? '')) ?>">
            <input type="hidden" name="slug" value="<?= admin_e((string) ($editingCourse['slug'] ?? '')) ?>">
            <input type="hidden" name="price" value="<?= admin_e((string) ($editingCourse['price'] ?? '0')) ?>">
            <input type="hidden" name="status" value="<?= admin_e($selectedStatus) ?>">
            <input type="hidden" name="featured_image" value="<?= admin_e((string) ($editingCourse['featured_image'] ?? '')) ?>">
            <input type="hidden" name="short_description" value="<?= admin_e((string) ($editingCourse['short_description'] ?? '')) ?>">
            <input type="hidden" name="author_name" value="<?= admin_e((string) ($editingCourse['author_name'] ?? '')) ?>">
            <input type="hidden" name="level_group" value="<?= admin_e((string) ($editingCourse['level'] ?? 'beginner')) ?>">
            <input type="hidden" name="level_group_custom" value="<?= admin_e((string) ($editingCourse['level_group_label'] ?? '')) ?>">
          <?php elseif ($isFrontCard): ?>
            <input type="hidden" name="action" value="save_course_basic">
            <div class="lg:col-span-2">
              <label class="block text-xs font-semibold mb-1">Hero Image Front Card (URL)</label>
              <input type="text" name="featured_image" class="w-full rounded-lg border-slate-300" value="<?= admin_e((string) ($editingCourse['featured_image'] ?? '')) ?>" placeholder="/assets/uploads/courses/...">
            </div>
            <div class="lg:col-span-2">
              <label class="block text-xs font-semibold mb-1">Atau Upload Hero Image</label>
              <input type="file" name="front_card_image_file" accept="image/jpeg,image/png,image/webp,image/gif" class="w-full rounded-lg border-slate-300">
            </div>
            <div class="lg:col-span-2">
              <label class="block text-xs font-semibold mb-1">Deskripsi Singkat</label>
              <textarea name="short_description" rows="3" class="w-full rounded-lg border-slate-300"><?= admin_e((string) ($editingCourse['short_description'] ?? '')) ?></textarea>
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">Author</label>
              <input type="text" name="author_name" class="w-full rounded-lg border-slate-300" value="<?= admin_e((string) ($editingCourse['author_name'] ?? '')) ?>" placeholder="Nama author">
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">Level Expertise / Group</label>
              <select name="level_group" class="w-full rounded-lg border-slate-300">
                <?php foreach (['beginner', 'intermediate', 'advanced', 'custom'] as $lv): ?>
                  <option value="<?= $lv ?>" <?= $selectedLevel === $lv ? 'selected' : '' ?>><?= $lv ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="lg:col-span-2">
              <label class="block text-xs font-semibold mb-1">Custom Group (opsional)</label>
              <input type="text" name="level_group_custom" class="w-full rounded-lg border-slate-300" value="<?= admin_e($selectedLevel === 'custom' ? $savedGroupLabel : '') ?>" placeholder="Contoh: Mahasiswa Polines">
            </div>
            <input type="hidden" name="title" value="<?= admin_e((string) ($editingCourse['title'] ?? '')) ?>">
            <input type="hidden" name="price" value="<?= admin_e((string) ($editingCourse['price'] ?? '0')) ?>">
            <input type="hidden" name="status" value="<?= admin_e($selectedStatus) ?>">
            <input type="hidden" name="slug" value="<?= admin_e((string) ($editingCourse['slug'] ?? '')) ?>">
          <?php else: ?>
            <input type="hidden" name="action" value="save_course_basic">
            <div>
              <label class="block text-xs font-semibold mb-1">Judul</label>
              <input type="text" name="title" class="w-full rounded-lg border-slate-300" value="<?= admin_e((string) ($editingCourse['title'] ?? '')) ?>" required>
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">Slug (URL)</label>
              <input type="text" name="slug" class="w-full rounded-lg border-slate-300" value="<?= admin_e((string) ($editingCourse['slug'] ?? '')) ?>" placeholder="basic-iot-esp32">
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">Harga</label>
              <input type="number" name="price" min="0" step="0.01" class="w-full rounded-lg border-slate-300" value="<?= admin_e((string) ($editingCourse['price'] ?? '0')) ?>">
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">Status</label>
              <select name="status" class="w-full rounded-lg border-slate-300">
                <?php foreach (['draft', 'published', 'archived'] as $s): ?>
                  <option value="<?= $s ?>" <?= $selectedStatus === $s ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <input type="hidden" name="featured_image" value="<?= admin_e((string) ($editingCourse['featured_image'] ?? '')) ?>">
            <input type="hidden" name="short_description" value="<?= admin_e((string) ($editingCourse['short_description'] ?? '')) ?>">
            <input type="hidden" name="author_name" value="<?= admin_e((string) ($editingCourse['author_name'] ?? '')) ?>">
            <input type="hidden" name="level_group" value="<?= admin_e((string) ($editingCourse['level'] ?? 'beginner')) ?>">
            <input type="hidden" name="level_group_custom" value="">
          <?php endif; ?>
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
        </form>
        <?php if ($isModule && isset($moduleKind) && $moduleKind === 'video'): ?>
          <div class="mt-4 rounded-xl border border-slate-200 bg-white p-4 space-y-3">
            <div class="font-semibold text-sm text-slate-700">Video dari Directory</div>
            <form method="post" class="grid lg:grid-cols-[1fr_auto] gap-2 items-end">
              <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
              <input type="hidden" name="action" value="video_pick_from_directory">
              <input type="hidden" name="course_id" value="<?= (int) ($editingCourse['id'] ?? 0) ?>">
              <input type="hidden" name="module_lesson_id" value="<?= (int) ($selectedModuleLesson['id'] ?? 0) ?>">
              <div>
                <label class="block text-xs font-semibold mb-1">Pilih Video</label>
                <select name="video_directory_file" class="w-full rounded-lg border-slate-300">
                  <option value="">-- Pilih video dari folder --</option>
                  <?php foreach ($videoDirectoryFiles as $videoFile): ?>
                    <option value="<?= admin_e((string) $videoFile) ?>" <?= ((string) $selectedModuleVideoFile === (string) $videoFile) ? 'selected' : '' ?>>
                      <?= admin_e((string) $videoFile) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <button class="px-4 py-2 rounded-lg bg-blue-800 text-white text-sm font-semibold">Gunakan Video</button>
            </form>

            <form method="post" class="grid lg:grid-cols-[1fr_auto] gap-2 items-end">
              <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
              <input type="hidden" name="action" value="video_rename_file">
              <input type="hidden" name="course_id" value="<?= (int) ($editingCourse['id'] ?? 0) ?>">
              <input type="hidden" name="module_lesson_id" value="<?= (int) ($selectedModuleLesson['id'] ?? 0) ?>">
              <div class="grid lg:grid-cols-2 gap-2">
                <div>
                  <label class="block text-xs font-semibold mb-1">File Video</label>
                  <select name="video_directory_file" class="w-full rounded-lg border-slate-300" required>
                    <option value="">-- Pilih video --</option>
                    <?php foreach ($videoDirectoryFiles as $videoFile): ?>
                      <option value="<?= admin_e((string) $videoFile) ?>" <?= ((string) $selectedModuleVideoFile === (string) $videoFile) ? 'selected' : '' ?>>
                        <?= admin_e((string) $videoFile) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div>
                <label class="block text-xs font-semibold mb-1">Rename Video (tanpa ekstensi)</label>
                <input type="text" name="video_rename_to" class="w-full rounded-lg border-slate-300" placeholder="Contoh: intro-modul-2" required>
                </div>
              </div>
              <button class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-semibold">Rename</button>
            </form>

            <form method="post" onsubmit="return confirm('apakah benar anda ingin menghapus video ini dari directory?');" class="grid lg:grid-cols-[1fr_auto] gap-2 items-end">
              <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
              <input type="hidden" name="action" value="video_delete_file">
              <input type="hidden" name="course_id" value="<?= (int) ($editingCourse['id'] ?? 0) ?>">
              <input type="hidden" name="module_lesson_id" value="<?= (int) ($selectedModuleLesson['id'] ?? 0) ?>">
              <div>
                <label class="block text-xs font-semibold mb-1">File Video</label>
                <select name="video_directory_file" class="w-full rounded-lg border-slate-300" required>
                  <option value="">-- Pilih video --</option>
                  <?php foreach ($videoDirectoryFiles as $videoFile): ?>
                    <option value="<?= admin_e((string) $videoFile) ?>" <?= ((string) $selectedModuleVideoFile === (string) $videoFile) ? 'selected' : '' ?>>
                      <?= admin_e((string) $videoFile) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <button class="px-4 py-2 rounded-lg bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700">Delete Video</button>
            </form>
          </div>
        <?php endif; ?>
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
        <?php if ($isChapter): ?>
          <div class="mt-6"></div>
          <form method="post" class="rounded-xl border border-slate-200 bg-white p-4 grid lg:grid-cols-2 gap-3">
            <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
            <input type="hidden" name="action" value="add_chapter_basic">
            <input type="hidden" name="course_id" value="<?= (int) ($editingCourse['id'] ?? 0) ?>">
            <div class="lg:col-span-2">
              <div class="font-semibold text-sm text-slate-700">Tambah Chapter Baru</div>
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">Nama Chapter Baru</label>
              <input type="text" name="chapter_title_new" class="w-full rounded-lg border-slate-300" placeholder="Contoh: Chapter 7" required>
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">Nomor Urut Chapter Baru</label>
              <input type="number" min="1" name="chapter_order_new" class="w-full rounded-lg border-slate-300" value="<?= max(1, count($moduleRows) + 1) ?>" required>
            </div>
            <div class="lg:col-span-2">
              <button class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-semibold">Tambah Chapter</button>
            </div>
          </form>
          <?php
            $selectedChapterIdForAdd = (int) ($selectedChapter['id'] ?? 0);
            $chapterLessonsCount = $selectedChapterIdForAdd > 0
              ? count((array) ($lessonsByModule[$selectedChapterIdForAdd] ?? []))
              : 0;
          ?>
          <div class="mt-4"></div>
          <form method="post" class="rounded-xl border border-slate-200 bg-white p-4 grid lg:grid-cols-2 gap-3">
            <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
            <input type="hidden" name="action" value="add_module_basic">
            <input type="hidden" name="course_id" value="<?= (int) ($editingCourse['id'] ?? 0) ?>">
            <input type="hidden" name="chapter_id" value="<?= $selectedChapterIdForAdd ?>">
            <div class="lg:col-span-2">
              <div class="font-semibold text-sm text-slate-700">Tambah Modul Baru ke Chapter Ini</div>
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">Nama Modul Baru</label>
              <input type="text" name="module_title_new" class="w-full rounded-lg border-slate-300" placeholder="Contoh: Modul 2.3 - Integrasi Sensor" required>
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">Nomor Urut Modul</label>
              <input type="number" min="1" step="1" name="module_order_new" class="w-full rounded-lg border-slate-300" value="<?= max(1, $chapterLessonsCount + 1) ?>" required>
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">Jenis Modul</label>
              <select name="module_kind_new" class="w-full rounded-lg border-slate-300">
                <option value="article">Article</option>
                <option value="video">Video</option>
                <option value="powerpoint">Power Point</option>
                <option value="quiz_multiple_choice">Quiz Multiple Choice</option>
                <option value="quiz_essay">Quiz Essay</option>
                <option value="quiz_submit_file">Quiz Submit File</option>
                <option value="test_multiple_choice">Test Multiple Choice</option>
                <option value="test_essay">Test Essay</option>
                <option value="test_submit_file">Test Submit File</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">Durasi (menit)</label>
              <input type="number" min="0" step="1" name="module_duration_minutes_new" class="w-full rounded-lg border-slate-300" value="0">
            </div>
            <div class="lg:col-span-2">
              <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="module_is_preview_new" value="1" class="rounded border-slate-300 text-blue-700">
                <span>Buka sebagai preview</span>
              </label>
            </div>
            <div class="lg:col-span-2">
              <button class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-semibold">Tambah Modul</button>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>
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
  <?php endif; ?>

  <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
    <div class="p-5 border-b border-slate-200 flex items-start justify-between gap-3">
      <div>
        <h2 class="font-bold text-lg">Daftar Kursus (maks 100 terbaru)</h2>
        <p class="text-sm text-slate-500 mt-1">Update status publikasi kursus secara langsung.</p>
      </div>
      <a href="<?= admin_e(admin_url('/admin/courses?mode=create')) ?>" class="inline-flex items-center px-3 py-2 rounded-lg bg-blue-800 text-white text-sm font-semibold hover:bg-blue-900 whitespace-nowrap">
        Tambah Kursus
      </a>
    </div>

    <div class="md:hidden p-4 space-y-3">
      <?php foreach ($rows as $idx => $r): ?>
        <div class="rounded-xl border border-slate-200 p-4">
          <div class="font-semibold text-slate-800"><?= ($idx + 1) ?>. <?= admin_e((string) $r['title']) ?></div>
          <div class="mt-1 text-xs text-slate-500">URL: <?= admin_e((string) (($r['slug'] ?? '') !== '' ? '/learning/' . (string) $r['slug'] : '-')) ?></div>
          <div class="text-xs text-slate-500">Kategori: <?= admin_e((string) ($r['category_name'] ?? '-')) ?></div>
          <div class="text-xs text-slate-500">Author: <?= admin_e((string) ($r['author_name'] ?? '-')) ?></div>
          <div class="text-xs text-slate-500">Student: <?= (int) ($r['student_count'] ?? 0) ?></div>
          <div class="mt-2 text-xs text-slate-500">Harga: Rp <?= number_format((float) $r['price'], 0, ',', '.') ?></div>
          <div class="text-xs text-slate-500">Published: <?= admin_e((string) ($r['published_at'] ?? '-')) ?></div>
          <div class="mt-2">
            <span class="px-2 py-1 rounded-full text-xs font-bold <?= $r['status'] === 'published' ? 'bg-emerald-100 text-emerald-700' : ($r['status'] === 'draft' ? 'bg-slate-100 text-slate-700' : 'bg-amber-100 text-amber-700') ?>">
              <?= admin_e((string) $r['status']) ?>
            </span>
          </div>
          <form method="post" class="mt-3 flex flex-col gap-2">
            <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
            <input type="hidden" name="action" value="status_update">
            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
            <select name="status" class="rounded-lg border-slate-300 text-sm w-full">
              <?php foreach (['draft', 'published', 'archived'] as $s): ?>
                <option value="<?= $s ?>" <?= $s === $r['status'] ? 'selected' : '' ?>><?= $s ?></option>
              <?php endforeach; ?>
            </select>
            <button class="px-3 py-2 bg-blue-800 text-white rounded-lg font-semibold w-full">Simpan</button>
          </form>
          <a href="<?= admin_e(admin_url('/admin/courses?course_id=' . (int) $r['id'])) ?>" class="mt-2 block text-center px-3 py-2 border border-slate-300 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50">Edit</a>
        </div>
      <?php endforeach; ?>
      <?php if (!$rows): ?>
        <div class="text-center text-sm text-slate-500 py-4">Belum ada data courses.</div>
      <?php endif; ?>
    </div>

    <div class="hidden md:block overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] tracking-wider">
        <tr>
          <th class="px-4 py-3 text-left">Urutan</th>
          <th class="px-4 py-3 text-left">Judul</th>
          <th class="px-4 py-3 text-left">URL</th>
          <th class="px-4 py-3 text-left">Kategori</th>
          <th class="px-4 py-3 text-left">Author</th>
          <th class="px-4 py-3 text-left">Student</th>
          <th class="px-4 py-3 text-left">Harga</th>
          <th class="px-4 py-3 text-left">Published</th>
          <th class="px-4 py-3 text-left">Status</th>
          <th class="px-4 py-3 text-right">Aksi</th>
        </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
        <?php foreach ($rows as $idx => $r): ?>
          <tr>
            <td class="px-4 py-3"><?= ($idx + 1) ?></td>
            <td class="px-4 py-3 font-semibold text-slate-800"><?= admin_e((string) $r['title']) ?></td>
            <td class="px-4 py-3 text-slate-500"><?= admin_e((string) (($r['slug'] ?? '') !== '' ? '/learning/' . (string) $r['slug'] : '-')) ?></td>
            <td class="px-4 py-3"><?= admin_e((string) ($r['category_name'] ?? '-')) ?></td>
            <td class="px-4 py-3"><?= admin_e((string) ($r['author_name'] ?? '-')) ?></td>
            <td class="px-4 py-3"><?= (int) ($r['student_count'] ?? 0) ?></td>
            <td class="px-4 py-3">Rp <?= number_format((float) $r['price'], 0, ',', '.') ?></td>
            <td class="px-4 py-3 text-slate-500"><?= admin_e((string) ($r['published_at'] ?? '-')) ?></td>
            <td class="px-4 py-3">
              <span class="px-2 py-1 rounded-full text-xs font-bold <?= $r['status'] === 'published' ? 'bg-emerald-100 text-emerald-700' : ($r['status'] === 'draft' ? 'bg-slate-100 text-slate-700' : 'bg-amber-100 text-amber-700') ?>">
                <?= admin_e((string) $r['status']) ?>
              </span>
            </td>
            <td class="px-4 py-3">
              <form method="post" class="flex flex-col sm:flex-row sm:justify-end gap-2">
                <a href="<?= admin_e(admin_url('/admin/courses?course_id=' . (int) $r['id'])) ?>" class="px-3 py-1.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 text-center">Edit</a>
                <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
                <input type="hidden" name="action" value="status_update">
                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                <select name="status" class="rounded-lg border-slate-300 text-sm">
                  <?php foreach (['draft', 'published', 'archived'] as $s): ?>
                    <option value="<?= $s ?>" <?= $s === $r['status'] ? 'selected' : '' ?>><?= $s ?></option>
                  <?php endforeach; ?>
                </select>
                <button class="px-3 py-1.5 bg-blue-800 text-white rounded-lg font-semibold">Simpan</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?>
          <tr><td colspan="10" class="px-4 py-5 text-center text-slate-500">Belum ada data courses.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
