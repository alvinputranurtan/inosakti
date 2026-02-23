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
          <input type="hidden" name="action" value="save_course_basic">
          <input type="hidden" name="id" value="<?= (int) ($editingCourse['id'] ?? 0) ?>">
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
          <?php $isFrontCard = $selectedEditSection === 'front-card'; ?>
          <?php $isLanding = $selectedEditSection === 'landing'; ?>
          <?php if ($isLanding): ?>
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
            <button class="px-4 py-2 rounded-lg bg-blue-800 text-white text-sm font-semibold">Simpan Kursus</button>
            <a href="<?= admin_e(admin_url('/admin/courses')) ?>" class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-white">Batal</a>
          </div>
        </form>
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
