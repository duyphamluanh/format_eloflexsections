<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Settings for format_eloflexsections
 *
 * @package    format_eloflexsections
 * @copyright  2023 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

use format_eloflexsections\constants;

if ($ADMIN->fulltree) {
    $url = new moodle_url('/admin/course/resetindentation.php', ['format' => 'eloflexsections']);
    $link = html_writer::link($url, get_string('resetindentation', 'admin'));
    $settings->add(new admin_setting_configcheckbox(
        'format_eloflexsections/indentation',
        new lang_string('indentation', 'format_topics'),
        new lang_string('indentation_help', 'format_topics').'<br />'.$link,
        1
    ));
    $settings->add(new admin_setting_configtext('format_eloflexsections/maxsectiondepth',
        get_string('maxsectiondepth', 'format_eloflexsections'),
        get_string('maxsectiondepthdesc', 'format_eloflexsections'), 10, PARAM_INT, 7));
    $settings->add(new admin_setting_configcheckbox('format_eloflexsections/showsection0titledefault',
        get_string('showsection0titledefault', 'format_eloflexsections'),
        get_string('showsection0titledefaultdesc', 'format_eloflexsections'), 0));
    $options = [
        constants::COURSEINDEX_FULL => get_string('courseindexfull', 'format_eloflexsections'),
        constants::COURSEINDEX_SECTIONS => get_string('courseindexsections', 'format_eloflexsections'),
        constants::COURSEINDEX_NONE => get_string('courseindexnone', 'format_eloflexsections'),
    ];
    $settings->add(new admin_setting_configselect('format_eloflexsections/courseindexdisplay',
        get_string('courseindexdisplay', 'format_eloflexsections'),
        get_string('courseindexdisplaydesc', 'format_eloflexsections'), 0, $options));
    $settings->add(new admin_setting_configcheckbox('format_eloflexsections/accordion',
        get_string('accordion', 'format_eloflexsections'),
        get_string('accordiondesc', 'format_eloflexsections'), 0));
    $settings->add(new admin_setting_configcheckbox('format_eloflexsections/cmbacklink',
        get_string('cmbacklink', 'format_eloflexsections'),
        get_string('cmbacklinkdesc', 'format_eloflexsections'), 0));
}
