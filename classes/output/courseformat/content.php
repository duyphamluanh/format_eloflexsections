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

namespace format_eloflexsections\output\courseformat;

use core_courseformat\external\get_state;
use course_modinfo;
use stdClass;
use core_courseformat\base as course_format;
use format_eloflexsections;

/**
 * Render a course content.
 *
 * @package   format_eloflexsections
 * @copyright 2022 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content extends \core_courseformat\output\local\content {

    /** @var \format_eloflexsections the course format class */
    protected $format;

    /** @var bool Eloflexsections format has add section. */
    protected $hasaddsection = true;

     /**
     * Constructor.
     *
     * @param course_format $format the coruse format
     */
    public function __construct(course_format $format) {
        parent::__construct($format);
        $modinfo = $this->format->get_modinfo();
        $sectionlist = $this->get_course_sections($modinfo);
        $sectionformatlist = $this->get_course_sections_options($modinfo);
        if(count($sectionformatlist) != count($sectionlist)) {
            $sectionformatlist = array_keys($sectionformatlist);
            foreach($sectionlist as $section) {
                if(!in_array((int)$section->id, $sectionformatlist)) {
                    $this->format->update_section_format_options(['id' => $section->id]);
                }
            }
            $sectionformatlist = $this->get_course_sections_options($modinfo);
        }
        $this->new_session_preferences($sectionformatlist, $modinfo);
        $this->register_alwaysexpand_session_preferences($sectionlist, $sectionformatlist);
    }

    /**
     * Template name for this exporter
     *
     * @param \renderer_base $renderer
     * @return string
     */
    public function get_template_name(\renderer_base $renderer): string {
        return 'format_eloflexsections/local/content';
    }

    /**
     * Export this data so it can be used as the context for a mustache template (core/inplace_editable).
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return \stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output) {
        $data = parent::export_for_template($output);

        // If we are on course view page for particular section.
        if ($this->format->get_viewed_section()) {
            // Do not display the "General" section when on a page of another section.
            $data->initialsection = null;

            // Add 'back to parent' control.
            $section = $this->format->get_section($this->format->get_viewed_section());
            if ($section->parent) {
                $sr = $this->format->find_collapsed_parent($section->parent);
                $url = $this->format->get_view_url($section->section, array('sr' => $sr));
                $data->backtosection = [
                    'url' => $url->out(false),
                    'sectionname' => $this->format->get_section_name($section->parent)
                ];
            } else {
                $sr = 0;
                $url = $this->format->get_view_url($section->section, array('sr' => $sr));
                $context = \context_course::instance($this->format->get_courseid());
                $data->backtocourse = [
                    'url' => $url->out(false),
                    'coursename' => format_string($this->format->get_course()->fullname, true, ['context' => $context]),
                ];
            }

            // Hide add section link below page content.
            $data->numsections = false;
        }
        $data->accordion = $this->format->get_accordion_setting() ? 1 : '';
        $data->mainsection = $this->format->get_viewed_section();

        return $data;
    }

    /**
     * Export sections array data.
     *
     * TODO: this is an exact copy of the parent function because get_sections_to_display() is private
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    protected function export_sections(\renderer_base $output): array {

        $format = $this->format;
        $course = $format->get_course();
        $modinfo = $this->format->get_modinfo();
        // Generate section list.
        $sections = [];
        $stealthsections = [];
        $numsections = $format->get_last_section_number();

        foreach ($this->get_sections_to_display($modinfo) as $sectionnum => $thissection) {
            // The course/view.php check the section existence but the output can be called
            // from other parts so we need to check it.
            if (!$thissection) {
                throw new \moodle_exception('unknowncoursesection', 'error',
                    course_get_url($course), format_string($course->fullname));
            }

            $section = new $this->sectionclass($format, $thissection);

            if ($sectionnum > $numsections) {
                // Activities inside this section are 'orphaned', this section will be printed as 'stealth' below.
                if (!empty($modinfo->sections[$sectionnum])) {
                    $stealthsections[] = $section->export_for_template($output);
                }
                continue;
            }

            if (!$format->is_section_visible($thissection)) {
                continue;
            }

            $sections[] = $section->export_for_template($output);
        }
        if (!empty($stealthsections)) {
            $sections = array_merge($sections, $stealthsections);
        }
        
        return $sections;
    }

    /**
     * Return an array of sections to display.
     *
     * This method is used to differentiate between display a specific section
     * or a list of them.
     *
     * @param course_modinfo $modinfo the current course modinfo object
     * @return \section_info[] an array of section_info to display
     */
    private function get_sections_to_display(course_modinfo $modinfo): array {
        $viewedsection = $this->format->get_viewed_section();
        return array_values(array_filter($modinfo->get_section_info_all(), function($s) use ($viewedsection) {
            return (!$s->section) ||
                (!$viewedsection && !$s->parent && $this->format->is_section_visible($s)) ||
                ($viewedsection && $s->section == $viewedsection);
        }));
    }

    /**
     */
    private function get_course_sections_options(course_modinfo $modinfo): array {
        global $DB;
        return $DB->get_records('course_format_options', ['courseid' => $modinfo->courseid, 'name' => 'alwaysexpand'], '', 'sectionid, name, value');
    }

    /**
     */
    private function get_course_sections(course_modinfo $modinfo): array {
        global $DB;
        return $DB->get_records('course_sections', ['course' => $modinfo->courseid], '', 'id,section');
    }

    /**
     */
    private function new_session_preferences($sectionformatlist, $modinfo) {
        global $SESSION;
        $format = $this->format;
        $coursesession = "newflexsection_".$modinfo->courseid;
        if(!isset($SESSION->$coursesession)) {
            $current_preference['contentcollapsed'] = array();
            foreach($sectionformatlist as $key => $section) {
                if($section->value == FORMAT_ELOFLEXSECTIONS_EXPANDED_ALWAYS_NO) {
                    $current_preference['contentcollapsed'][] = $section->sectionid;
                }
            }
            $format->set_sections_preference('contentcollapsed', array_values($current_preference['contentcollapsed']));
            $format->set_sections_preference('indexcollapsed', array_values($current_preference['contentcollapsed']));
            $SESSION->$coursesession = true;
        }
    }    

    private function register_alwaysexpand_session_preferences($sectionlist, $sectionformatlist) {
        $format = $this->format;
        $current_preference = $format->get_sections_preferences_by_preference();
        if($current_preference == null) {
            $current_preference['contentcollapsed'] = array();
        }
        foreach($sectionlist as $section) {
            if($sectionformatlist[$section->id]->value == FORMAT_ELOFLEXSECTIONS_EXPANDED_ALWAYS_YES) {
                $key = array_search($section->id, $current_preference['contentcollapsed']);
                if($key !== false){
                    unset($current_preference['contentcollapsed'][$key]);
                }
            }
        }
        $format->set_sections_preference('contentcollapsed', array_values($current_preference['contentcollapsed']));
        $format->set_sections_preference('indexcollapsed', array_values($current_preference['contentcollapsed']));
    } 

}
