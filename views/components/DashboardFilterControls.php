<?php
/**
 * Component: DashboardFilterControls.php
 *
 * This component renders filter controls for a dashboard or table.
 * It expects the following variables to be set by the calling script:
 *
 * @var array $filterGroups An array of filter groups. Each group is an associative array with:
 *      'label'         => (string) The label for the filter dropdown (e.g., 'Filter by Card Type:').
 *      'name'          => (string) The name attribute for the select element (e.g., 'card_type').
 *      'options'       => (array) An array of strings for the dropdown options.
 *      'selectedValue' => (string|null) The currently selected value for this filter, if any.
 *      'allLabel'      => (string) The label for the 'all' option (e.g., 'All Card Types').
 *
 * @var string $formAction The URL the form should submit to (e.g., 'index.php').
 * @var string $formMethod The HTTP method for the form (e.g., 'GET').
 * @var string $hiddenPathValue The value for the hidden 'path' input (e.g., 'dashboardNew').
 * @var string|null $clearFilterLink The URL to clear all filters. If null, the clear button won't show.
 * @var string|null $activeFilterMessage A message to display if any filter is active (e.g., "Showing: Credit Cards").
 */

// Default values for safety, though they should be provided by the caller.
$filterGroups = $filterGroups ?? [];
$formAction = $formAction ?? 'index.php';
$formMethod = $formMethod ?? 'GET';
$hiddenPathValue = $hiddenPathValue ?? '';
$clearFilterLink = $clearFilterLink ?? null;
$activeFilterMessage = $activeFilterMessage ?? null; 

$isAnyFilterActive = false;
foreach ($filterGroups as $group) {
    if (!empty($group['selectedValue'])) {
        $isAnyFilterActive = true;
        break;
    }
}

?>

<?php if (!empty($filterGroups)): ?>
<form method="<?= htmlspecialchars($formMethod) ?>" action="<?= htmlspecialchars($formAction) ?>" class="mb-3">
    <input type="hidden" name="path" value="<?= htmlspecialchars($hiddenPathValue) ?>">
    <div class="row align-items-end g-2"> <?php // g-2 for gutter between columns ?>
        <?php foreach ($filterGroups as $index => $group): ?>
            <div class="col-md-auto"> <?php // col-md-auto for auto-sizing based on content ?>
                <label for="filter_<?= htmlspecialchars($group['name']) ?>_<?= $index ?>" class="form-label visually-hidden"><?= htmlspecialchars($group['label']) ?></label>
                <select name="<?= htmlspecialchars($group['name']) ?>" id="filter_<?= htmlspecialchars($group['name']) ?>_<?= $index ?>" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value=""><?= htmlspecialchars($group['allLabel'] ?? 'All') ?></option>
                    <?php foreach ($group['options'] as $option): ?>
                        <option value="<?= htmlspecialchars($option) ?>"
                            <?= (isset($group['selectedValue']) && $group['selectedValue'] === $option) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($option) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endforeach; ?>

        <?php if ($isAnyFilterActive && $clearFilterLink): ?>
            <div class="col-md-auto">
                <a href="<?= htmlspecialchars($clearFilterLink) ?>" class="btn btn-sm btn-outline-secondary">Clear Filters</a>
            </div>
        <?php endif; ?>
    </div>
    <?php if ($activeFilterMessage): ?>
        <div class="mt-2">
            <small class="text-muted"><?= htmlspecialchars($activeFilterMessage) ?></small>
        </div>
    <?php endif; ?>
</form>
<?php endif; ?>
