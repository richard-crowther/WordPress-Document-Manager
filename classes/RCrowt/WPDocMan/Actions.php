<?php
/**
 * Created by PhpStorm.
 * User: Richard
 * Date: 26/07/2015
 * Time: 15:47
 */

namespace RCrowt\WPDocMan;


class Actions
{

    const UPLOAD_FIELD = 'rcrowt_docman_upload';


    public function __construct()
    {
        // Add class methods as actions.
        foreach (get_class_methods($this) as $method)
            if (substr($method, 0, 1) != '_')
                add_action($method, [$this, $method]);
    }

    /**
     * Call this action when WordPress adds meta boxes to a Post Edit page.
     */
    public function add_meta_boxes()
    {
        add_meta_box('document_file', 'Document File', function (\WP_Post $post) {

            $document = new \RCrowt\WPDocMan\PostMetaDocument($post);

            echo '<p class="description">Upload your PDF Document here</p>';
            echo '<input type="file" id="' . self::UPLOAD_FIELD . '" name="' . self::UPLOAD_FIELD . '" value="" size="25"/>';
            if ($document->isFile()) {
                echo '<p><b>Download:</b> <a href="' . $document->getFileUrl() . '">' . $document->getFileUrl() . '</a><br/><b>Last Modified:</b> ' . date('l jS F Y H:i:s', filemtime($document->getFilePath())) . '<br/><b>File Size: </b>' . $document->getFileSize() . '</p>';
            } else {
                echo '<p style="color:#FF0000;">No Document has been uploaded.</p>';
            }
        }, 'post');
    }

    /**
     * Call this action when a post-edit form is generated to allow uploads.
     */
    public function post_edit_form_tag()
    {
        echo ' enctype="multipart/form-data"';
    }

    /**
     * Call this method when an post is saved/uplaoded.
     * @param $id
     * @return mixed
     */
    public function save_post($id)
    {

        // WordPress Security Verification
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $id;
        if (!current_user_can('edit_page', $id)) return $id;

        // Check Field has been submitted.
        if (isset($_FILES[self::UPLOAD_FIELD]['tmp_name'], $_FILES[self::UPLOAD_FIELD]['name'], $_FILES[self::UPLOAD_FIELD]['error'])) {

            // Don't continue if no file was uploaded.
            if ($_FILES[self::UPLOAD_FIELD]['error'] == UPLOAD_ERR_NO_FILE) return $id;

            // Save the file or remove the current one on error.
            $doc = new \RCrowt\WPDocMan\PostMetaDocument(get_post($id));
            if (isset($_FILES['rcrowt_docman_upload']['error']) && $_FILES['rcrowt_docman_upload']['error']) {
                $doc->removeFile();
            } else {
                $doc->setFile($_FILES['rcrowt_docman_upload']['tmp_name'], $_FILES['rcrowt_docman_upload']['name']);
            }
        }

        return $id;

    }

}