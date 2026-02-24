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
            <?php elseif ($moduleKind === 'powerpoint'): ?>
              <div class="lg:col-span-2">
                <label class="block text-xs font-semibold mb-1">Link PowerPoint</label>
                <input type="text" name="module_ppt_url" class="w-full rounded-lg border-slate-300" value="<?= admin_e((string) ($selectedModuleLesson['content_url'] ?? '')) ?>" placeholder="/assets/uploads/courses/presentations/slide.pptx atau link embed">
              </div>
              <div class="lg:col-span-2">
                <label class="block text-xs font-semibold mb-1">Atau Upload PowerPoint</label>
                <input type="file" name="module_ppt_file" accept=".ppt,.pptx,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation" class="w-full rounded-lg border-slate-300">
                <p class="mt-1 text-xs text-slate-500">Format yang didukung: ppt, pptx.</p>
              </div>
              <div class="lg:col-span-2">
                <label class="block text-xs font-semibold mb-1">Preview File</label>
                <?php $previewPptUrl = trim((string) ($selectedModuleLesson['content_url'] ?? '')); ?>
                <?php if ($previewPptUrl !== ''): ?>
                  <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm">
                    <div class="text-slate-700">File saat ini: <span class="font-semibold"><?= admin_e(basename(parse_url($previewPptUrl, PHP_URL_PATH) ?: $previewPptUrl)) ?></span></div>
                    <a class="mt-2 inline-flex items-center px-3 py-2 rounded-lg bg-blue-800 text-white text-xs font-semibold hover:bg-blue-900" href="<?= admin_e($previewPptUrl) ?>" target="_blank" rel="noopener">Buka File</a>
                  </div>
                <?php else: ?>
                  <div class="rounded-lg border border-slate-200 bg-slate-100 text-slate-500 text-sm p-4">Belum ada file PowerPoint untuk dipreview.</div>
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
