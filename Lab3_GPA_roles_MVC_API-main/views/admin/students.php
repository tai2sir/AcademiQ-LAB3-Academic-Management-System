<?php
declare(strict_types=1);
/** @var list<array<string,mixed>> $students */
/** @var ?array<string,mixed> $edit */
/** @var string $search */
/** @var array<string,int> $pagination */
/** @var int $total */
?>
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold"><?= $edit ? 'Edit student' : 'New student' ?></div>
            <div class="card-body">
                <?php if ($edit): ?>
                    <form method="post" class="vstack gap-3">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= e((string) $edit['id']) ?>">
                        <div>
                            <label class="form-label">Name</label>
                            <input class="form-control" name="name" value="<?= e((string) $edit['name']) ?>" required>
                        </div>
                        <div>
                            <label class="form-label">Email</label>
                            <input class="form-control" type="email" name="email" value="<?= e((string) $edit['email']) ?>" required>
                        </div>
                        <div>
                            <label class="form-label">New password</label>
                            <input class="form-control" type="password" name="password" placeholder="Leave blank to keep current" minlength="8">
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary">Save</button>
                            <a class="btn btn-outline-secondary" href="<?= e(url('admin_students')) ?>">Cancel</a>
                        </div>
                    </form>
                <?php else: ?>
                    <form method="post" class="vstack gap-3">
                        <input type="hidden" name="action" value="create">
                        <div>
                            <label class="form-label">Name</label>
                            <input class="form-control" name="name" required>
                        </div>
                        <div>
                            <label class="form-label">Email</label>
                            <input class="form-control" type="email" name="email" required>
                        </div>
                        <div>
                            <label class="form-label">Password</label>
                            <input class="form-control" type="password" name="password" required minlength="8">
                        </div>
                        <button class="btn btn-primary">Create</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <form class="row g-2 align-items-center" method="get" action="<?= e(url('admin_students')) ?>">
                    <input type="hidden" name="page" value="admin_students">
                    <div class="col">
                        <input class="form-control" name="q" value="<?= e($search) ?>" placeholder="Search name or email">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-outline-primary" type="submit">Search</button>
                    </div>
                </form>
                <div class="small text-muted mt-2"><?= e((string) $total) ?> total</div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 data-table">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($students as $p): ?>
                        <tr>
                            <td><?= e((string) $p['name']) ?></td>
                            <td><?= e((string) $p['email']) ?></td>
                            <td class="text-end text-nowrap">
                                <a class="btn btn-sm btn-outline-primary" href="<?= e(url('admin_students', ['edit' => (string) $p['id'], 'q' => $search, 'p' => (string) $pagination['page']])) ?>">Edit</a>
                                <form method="post" class="d-inline" onsubmit="return confirm('Delete student?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= e((string) $p['id']) ?>">
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($pagination['total_pages'] > 1): ?>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                    <span class="small text-muted">Page <?= e((string) $pagination['page']) ?> / <?= e((string) $pagination['total_pages']) ?></span>
                    <div class="btn-group">
                        <?php
                        $prev = max(1, $pagination['page'] - 1);
                        $next = min($pagination['total_pages'], $pagination['page'] + 1);
                        ?>
                        <a class="btn btn-sm btn-outline-secondary <?= $pagination['page'] <= 1 ? 'disabled' : '' ?>"
                           href="<?= e(url('admin_students', ['p' => (string) $prev, 'q' => $search])) ?>">Prev</a>
                        <a class="btn btn-sm btn-outline-secondary <?= $pagination['page'] >= $pagination['total_pages'] ? 'disabled' : '' ?>"
                           href="<?= e(url('admin_students', ['p' => (string) $next, 'q' => $search])) ?>">Next</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
