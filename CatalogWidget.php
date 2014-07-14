<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Controller to manage Discipline Api requests
 *
 * @category Controller
 * @author   Alexis Saransig
 */
class Controller_CatalogWidget extends Controller_Rest {
    // const BASE_DIR = '/var/www/Metrodigi/widget-catalog/catalog';
    const BASE_DIR = '../../../../../Users/snagaslaeva/Desktop/Pearson/widget-catalog/catalog';
    /**
     * Handle GET requests.
     */

    public function action_index() {
        try {
            $id = $this->request->param('id');
            $result = array();
            // If id provided search discipline with that id
            if ($id) {
                $catalogwidget = ORM::factory('CatalogWidget', $id);

                // Check if discipline exists
                if (!$catalogwidget->loaded()) {
                    throw HTTP_Exception::factory(404, 'Resourse not found');
                }

                $result = $catalogwidget->as_array();
            }
            // If id not provided return all disciplines
            else {
                foreach(scandir(self::BASE_DIR) as $file) {
                    if ($file != '.' && $file != '..' && $file != '.DS_Store') {
                        $filter = (isset($_REQUEST['author'])) ? $_REQUEST['author'] : 'All';
                        // $filter = (isset($_REQUEST['check'])) ? $_REQUEST['check'] : 'assess';
                        if ($this->fitsFilter($filter, $file)) {
                            $obj = new stdClass();
                            $obj->file = $file;
                            $obj->discipline = $this->filenameToDiscipline($file);
                            $obj->assessment = $this->isThisAnAssessment($file);
                            $obj->widgetType = $this->firstWordToType($file);

                            $results[] = $obj;
                        }
                    }
                }
            }
            $this->rest_output($results);
        } catch (Kohana_HTTP_Exception $khe) {
            $this->_error($khe);
            return;
        } catch (Kohana_Exception $e) {
            $this->_error('An internal error has occurred' .  $e , 500);
        }
    }

    private function fitsFilter($filter, $value) {
        if ($filter == 'All') {
            return true;
        }

        $fileParts = explode('-', $value);

        foreach ($fileParts as $n) {
            if ($filter == $n) {
                return true;
            }
        }

        // foreach($fileParts as $assess) {
        //     if ($filter == $assess) {
        //         return true;
        //     }
        // }

        return false;
    }


    public function filenameToDiscipline($file){
         $category = 'n/a';

        if (preg_match('/\-brands/' , $file)) {
            $category = 'U.S. History';
        } elseif (preg_match('/\-(henslin|macionis)/' , $file)) {
            $category = 'Sociology';
        } elseif (preg_match('/\-bonds/' , $file)) {
            $category = 'Music';
        } elseif (preg_match('/\-(ciccarelli|feldman)/' , $file)) {
            $category = 'Psychology';
        } elseif (preg_match('/\-(stokstad|sayre|prebles)/' , $file)) {
            $category = 'Arts';
        } elseif (preg_match('/\-beebe/' , $file)) {
            $category = 'Communication';
        };
        return $category;
    }

    public function isThisAnAssessment($file){
        $answer = 'n/a';
        if (preg_match('/\-assess/' , $file)) {
            $answer = 'Yes';
        } else {
            $answer = 'No';
        };
        return $answer;
    }

    public function firstWordToType($file){
        $parts = explode('-', $file);
        if (count($parts)) {
            return $parts[0];
        }
        // return '';
    }
}




