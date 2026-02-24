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
