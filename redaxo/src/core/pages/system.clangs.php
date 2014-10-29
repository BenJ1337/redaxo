<?php

/**
 * Verwaltung der Content Sprachen
 * @package redaxo5
 */

$content = '';
$message = '';

// -------------- Defaults
$clang_id   = rex_request('clang_id', 'int');
$clang_code = rex_request('clang_code', 'string');
$clang_name = rex_request('clang_name', 'string');
$clang_prio = rex_request('clang_prio', 'int');
$func       = rex_request('func', 'string');

// -------------- Form Submits
$add_clang_save  = rex_post('add_clang_save', 'boolean');
$edit_clang_save = rex_post('edit_clang_save', 'boolean');


$warning = '';
$info = '';

// ----- delete clang
if ($func == 'deleteclang' && $clang_id != '') {
    try {
        if (rex_clang::exists($clang_id)) {
            rex_clang_service::deleteCLang($clang_id);
            $info = rex_i18n::msg('clang_deleted');
            $func = '';
            unset ($clang_id);
        }

    } catch (rex_functional_exception $e) {
        echo rex_view::error($e->getMessage());

    }

}

// ----- add clang
if ($add_clang_save || $edit_clang_save) {
    if ($clang_code == '') {
        $warning = rex_i18n::msg('enter_code');
        $func = $add_clang_save ? 'addclang' : 'editclang';
    } elseif ($clang_name == '') {
        $warning = rex_i18n::msg('enter_name');
        $func = $add_clang_save ? 'addclang' : 'editclang';
    } elseif ($add_clang_save) {
        $info = rex_i18n::msg('clang_created');
        rex_clang_service::addCLang($clang_code, $clang_name, $clang_prio);
        unset($clang_id);
        $func = '';
    } else {
        if (rex_clang::exists($clang_id)) {
            rex_clang_service::editCLang($clang_id, $clang_code, $clang_name, $clang_prio);
            $info = rex_i18n::msg('clang_edited');
            $func = '';
            unset ($clang_id);
        }
    }
}

if ($info != '') {
    $message .= rex_view::info($info);
}

if ($warning != '') {
    $message .= rex_view::warning($warning);
}


$content .= '
            <div class="rex-form" id="rex-form-system-language">
            <form action="' . rex_url::currentBackendPage() . '" method="post">
        ';

if ($func == 'addclang' || $func == 'editclang') {
    $legend = $func == 'addclang' ? rex_i18n::msg('clang_add') : rex_i18n::msg('clang_edit');
    $content .= '
                <fieldset>
                    <input type="hidden" name="clang_id" value="' . $clang_id . '" />
            ';
}


$content .= '
        <table class="rex-table rex-table-responsive rex-table-striped">
            <caption>' . rex_i18n::msg('clang_caption') . '</caption>
            <thead>
                <tr>
                    <th><a href="' . rex_url::currentBackendPage(['func' => 'addclang']) . '#clang"' . rex::getAccesskey(rex_i18n::msg('clang_add'), 'add') . '><i class="rex-icon rex-icon-add-language"></i></a></th>
                    <th>ID</th>
                    <th>' . rex_i18n::msg('clang_code') . '</th>
                    <th>' . rex_i18n::msg('clang_name') . '</th>
                    <th>' . rex_i18n::msg('clang_priority') . '</th>
                    <th colspan="2">' . rex_i18n::msg('clang_function') . '</th>
                </tr>
            </thead>
            <tbody>
    ';

// Add form
if ($func == 'addclang') {
    //ggf wiederanzeige des add forms, falls ungueltige id uebermittelt
    $content .= '
                <tr class="rex-active">
                    <td><i class="rex-icon rex-icon-language"></i></td>
                    <td data-title="ID">–</td>
                    <td data-title="' . rex_i18n::msg('clang_code') . '"><input class="rex-form-control" type="text" id="rex-form-clang-code" name="clang_code" value="' . htmlspecialchars($clang_code) . '" autofocus /></td>
                    <td data-title="' . rex_i18n::msg('clang_name') . '"><input class="rex-form-control" type="text" id="rex-form-clang-name" name="clang_name" value="' . htmlspecialchars($clang_name) . '" /></td>
                    <td data-title="' . rex_i18n::msg('clang_priority') . '"><input class="rex-form-control" type="text" id="rex-form-clang-prio" name="clang_prio" value="' . ($clang_prio ?: rex_clang::count() + 1) . '" /></td>
                    <td colspan="2"><button class="rex-button rex-button-save" type="submit" name="add_clang_save"' . rex::getAccesskey(rex_i18n::msg('clang_add'), 'save') . ' value="1">' . rex_i18n::msg('clang_add') . '</button></td>
                </tr>
            ';
}
foreach (rex_clang::getAll() as $lang_id => $lang) {

    $add_td = '';
    $add_td = '<td data-title="ID">' . $lang_id . '</td>';

    $delLink = rex_i18n::msg('delete');
    if ($lang_id == rex_clang::getStartId()) {
     $delLink = '<span class="rex-delete rex-text-muted"><i class="rex-icon rex-icon-delete"></i> ' . $delLink . '</span>';
    } else {
        $delLink = '<a class="rex-delete" href="' . rex_url::currentBackendPage(['func' => 'deleteclang', 'clang_id' => $lang_id]) . '" data-confirm="' . rex_i18n::msg('delete') . ' ?"><i class="rex-icon rex-icon-delete"></i> ' . $delLink . '</a>';
    }

    // Edit form
    if ($func == 'editclang' && $clang_id == $lang_id) {
        $content .= '
                    <tr class="rex-active">
                        <td><i class="rex-icon rex-icon-language"></i></td>
                        ' . $add_td . '
                        <td data-title="' . rex_i18n::msg('clang_code') . '"><input class="rex-form-control" type="text" id="rex-form-clang-code" name="clang_code" value="' . htmlspecialchars($lang->getCode()) . '" autofocus /></td>
                        <td data-title="' . rex_i18n::msg('clang_name') . '"><input class="rex-form-control" type="text" id="rex-form-clang-name" name="clang_name" value="' . htmlspecialchars($lang->getName()) . '" /></td>
                        <td data-title="' . rex_i18n::msg('clang_priority') . '"><input class="rex-form-control" type="text" id="rex-form-clang-prio" name="clang_prio" value="' . htmlspecialchars($lang->getPriority()) . '" /></td>
                        <td colspan="2"><button class="rex-button rex-button-save" type="submit" name="edit_clang_save"' . rex::getAccesskey(rex_i18n::msg('clang_update'), 'save') . ' value="1">' . rex_i18n::msg('clang_update') . '</button></td>
                    </tr>';

    } else {
        $editLink = rex_url::currentBackendPage(['func' => 'editclang', 'clang_id' => $lang_id]) . '#clang';

        $content .= '
                    <tr>
                        <td><a href="' . $editLink . '" title="' . htmlspecialchars($clang_name) . '"><i class="rex-icon rex-icon-language"></i></a></td>
                        ' . $add_td . '
                        <td data-title="' . rex_i18n::msg('clang_code') . '">' . htmlspecialchars($lang->getCode()) . '</td>
                        <td data-title="' . rex_i18n::msg('clang_name') . '">' . htmlspecialchars($lang->getName()) . '</td>
                        <td data-title="' . rex_i18n::msg('clang_priority') . '">' . htmlspecialchars($lang->getPriority()) . '</td>
                        <td><a href="' . $editLink . '"><i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit') . '</a></td>
                        <td>' . $delLink . '</td>
                    </tr>';
    }
}

$content .= '
        </tbody>
    </table>';

if ($func == 'addclang' || $func == 'editclang') {
    $content .= '
                </fieldset>';
}

$content .= '
            </form>
            </div>';

echo $message;
echo rex_view::content('block', $content, '', $params = ['flush' => true]);
