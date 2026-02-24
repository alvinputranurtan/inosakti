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
        <?php if ($isModule && isset($moduleKind) && $moduleKind === 'powerpoint'): ?>
          <div class="mt-4 rounded-xl border border-slate-200 bg-white p-4 space-y-3">
            <div class="font-semibold text-sm text-slate-700">PowerPoint dari Repository</div>
            <form method="post" class="grid lg:grid-cols-[1fr_auto] gap-2 items-end">
              <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
              <input type="hidden" name="action" value="ppt_pick_from_directory">
              <input type="hidden" name="course_id" value="<?= (int) ($editingCourse['id'] ?? 0) ?>">
              <input type="hidden" name="module_lesson_id" value="<?= (int) ($selectedModuleLesson['id'] ?? 0) ?>">
              <div>
                <label class="block text-xs font-semibold mb-1">Pilih File PowerPoint</label>
                <select name="ppt_directory_file" class="w-full rounded-lg border-slate-300">
                  <option value="">-- Pilih file dari repository --</option>
                  <?php foreach ($pptDirectoryFiles as $pptFile): ?>
                    <option value="<?= admin_e((string) $pptFile) ?>" <?= ((string) $selectedModulePptFile === (string) $pptFile) ? 'selected' : '' ?>>
                      <?= admin_e((string) $pptFile) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <button class="px-4 py-2 rounded-lg bg-blue-800 text-white text-sm font-semibold">Gunakan File</button>
            </form>

            <form method="post" class="grid lg:grid-cols-[1fr_auto] gap-2 items-end">
              <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
              <input type="hidden" name="action" value="ppt_rename_file">
              <input type="hidden" name="course_id" value="<?= (int) ($editingCourse['id'] ?? 0) ?>">
              <input type="hidden" name="module_lesson_id" value="<?= (int) ($selectedModuleLesson['id'] ?? 0) ?>">
              <div class="grid lg:grid-cols-2 gap-2">
                <div>
                  <label class="block text-xs font-semibold mb-1">File PowerPoint</label>
                  <select name="ppt_directory_file" class="w-full rounded-lg border-slate-300" required>
                    <option value="">-- Pilih file --</option>
                    <?php foreach ($pptDirectoryFiles as $pptFile): ?>
                      <option value="<?= admin_e((string) $pptFile) ?>" <?= ((string) $selectedModulePptFile === (string) $pptFile) ? 'selected' : '' ?>>
                        <?= admin_e((string) $pptFile) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-semibold mb-1">Rename File (tanpa ekstensi)</label>
                  <input type="text" name="ppt_rename_to" class="w-full rounded-lg border-slate-300" placeholder="Contoh: modul-3-intro" required>
                </div>
              </div>
              <button class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-semibold">Rename</button>
            </form>

            <form method="post" onsubmit="return confirm('apakah benar anda ingin menghapus file PowerPoint ini dari repository?');" class="grid lg:grid-cols-[1fr_auto] gap-2 items-end">
              <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
              <input type="hidden" name="action" value="ppt_delete_file">
              <input type="hidden" name="course_id" value="<?= (int) ($editingCourse['id'] ?? 0) ?>">
              <input type="hidden" name="module_lesson_id" value="<?= (int) ($selectedModuleLesson['id'] ?? 0) ?>">
              <div>
                <label class="block text-xs font-semibold mb-1">File PowerPoint</label>
                <select name="ppt_directory_file" class="w-full rounded-lg border-slate-300" required>
                  <option value="">-- Pilih file --</option>
                  <?php foreach ($pptDirectoryFiles as $pptFile): ?>
                    <option value="<?= admin_e((string) $pptFile) ?>" <?= ((string) $selectedModulePptFile === (string) $pptFile) ? 'selected' : '' ?>>
                      <?= admin_e((string) $pptFile) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <button class="px-4 py-2 rounded-lg bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700">Delete File</button>
            </form>
          </div>
        <?php endif; ?>
