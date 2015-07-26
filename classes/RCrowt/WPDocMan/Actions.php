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

    public function __construct()
    {
        // Add class methods as actions.
        foreach (get_class_methods($this) as $method)
            if (substr($method, 0, 1) != '_')
                add_action($method, [$this, $method]);
    }

    /**
     * Call this action when WordPress initialises.
     */
    public function init()
    {
        register_post_type('document', [
            'labels' => ['name' => 'Documents', 'singular_name' => 'Document'],
            'public' => true,
            'has_archive' => false,
            'supports' => ['title', 'excerpt'],
            'taxonomies' => ['category']
        ]);
    }

    /**
     * Call this action when WordPress adds meta boxes to a Post Edit page.
     */
    public function add_meta_boxes_document()
    {
        add_meta_box('document_file', 'Document File', function (\WP_Post $post, $info) {

            $document = new \RCrowt\WPDocMan\PostMetaDocument($post);

            echo '<p class="description">Upload your PDF Document here</p>';
            echo '<input type="file" id="rcrowt_docman_upload" name="rcrowt_docman_upload" value="" size="25"/>';
            if ($document->isFile()) {
                echo '<p><b>Download:</b> <a href="' . $document->getFileUrl() . '">' . $document->getFileUrl() . '</a><br/>
                <b>Last Modified:</b> ' . date('l jS F Y H:i:s', filemtime($document->getFilePath())) . '<br/>
                <b>File Size: </b>' . $document->getFileSize() . '</p>';
            } else {
                echo '<p>No Document has been uploaded yet.</p>';
            }
        }, 'document');
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

        /* --- security verification --- */
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $id;
        if (!current_user_can('edit_page', $id)) return $id;
        /* - end security verification - */

        if (isset($_FILES['rcrowt_docman_upload']['tmp_name'], $_FILES['rcrowt_docman_upload']['name'])) {
            $doc = new \RCrowt\WPDocMan\PostMetaDocument(get_post($id));

            // Check for Errors with Upload.
            if (isset($_FILES['rcrowt_docman_upload']['error']) && $_FILES['rcrowt_docman_upload']['error']) {
                $doc->removeFile();
            } else {
                $doc->setFile($_FILES['rcrowt_docman_upload']['tmp_name'], $_FILES['rcrowt_docman_upload']['name']);
            }
        }

    }

}