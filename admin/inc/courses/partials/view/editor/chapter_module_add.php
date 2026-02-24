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
