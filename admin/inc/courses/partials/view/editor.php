<?php if ($isCreateMode || $editingCourse): ?>
<?php require __DIR__ . '/editor/layout_start.php'; ?>
<?php require __DIR__ . '/editor/section_fields.php'; ?>
<?php require __DIR__ . '/editor/form_actions.php'; ?>
        </form>
<?php require __DIR__ . '/editor/video_directory.php'; ?>
<?php require __DIR__ . '/editor/modals.php'; ?>
<?php require __DIR__ . '/editor/chapter_module_add.php'; ?>
      </div>
    </div>
<?php require __DIR__ . '/editor/scripts.php'; ?>
<?php endif; ?>
