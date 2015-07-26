<?php
namespace RCrowt\WPDocMan;

/**
 * Class PostMetaDocument
 * @package RCrowt\WPDocMan
 */
class PostMetaDocument
{
    /**
     * @var \WP_Post;
     */
    protected $post;

    /**
     * @var string Meta Key.
     */
    protected $meta_key;

    /**
     * Document constructor.
     */
    public function __construct(\WP_Post $post, $meta_key = 'rcrowt_document')
    {
        $this->post = $post;
        $this->meta_key = $meta_key;
    }

    /**
     * Get the Document File Path.
     * @return null|string
     */
    public function getFilePath()
    {
        $filename = get_post_meta($this->post->ID, $this->meta_key, true);
        if (!$filename) return null;

        // Convert to Path.
        $dir = wp_upload_dir();
        return $dir['basedir'] . $filename;
    }

    /**
     * Get the Document File URL.
     * @return null|string
     */
    public function getFileUrl()
    {
        $filename = get_post_meta($this->post->ID, $this->meta_key, true);
        if (!$filename) return null;

        // Convert to URL.
        $dir = wp_upload_dir();
        return $dir['baseurl'] . $filename;
    }

    /**
     * Get the lowercase file extension of the document.
     * @return null|string
     */
    public function getFileExtension()
    {
        $path = get_post_meta($this->post->ID, $this->meta_key, true);
        if ($path) return self::_getFileExtension($path);
        else return null;
    }

    /**
     * Get the URL for a 32x32 icon for the Document.
     * @return string
     */
    public function getFileIconUrl()
    {
        $filename = 'icons/' . $this->getFileExtension() . '.png';
        if (file_exists(Helpers::getPluginPath(__FILE__, $filename))) return Helpers::getPluginUrl(__FILE__, $filename);
        else return Helpers::getPluginUrl(__FILE__, 'icons/default.png');
    }

    /**
     * Get the file size as a string.
     * @param null $format Specify the output format from [bytes|kb|mb] or NULL (Auto)
     */
    public function getFileSize($format = null)
    {
        // Check file exists.
        $filename = $this->getFilePath();
        if ($filename === null || !$this->isFile()) return null;

        // Get the Size.
        $filesize = filesize($filename);

        // Select the correct size if auto.
        if (!in_array($format, ['bytes', 'kb', 'mb'])) {
            if ($filesize >= 1024 * 1024) $format = 'mb';
            elseif ($filesize > 1024) $format = 'kb';
            else $format = 'bytes';
        }

        // Output the size
        if ($format == 'mb') return number_format($filesize / 1024 / 1024) . ' Mb';
        elseif ($format == 'kb') return number_format($filesize / 1024) . ' kb';
        else  return number_format($filesize) . ' bytes';
    }

    /**
     * Save an uploaded file as the document.
     * @param $tmp_name string Temporary Filename
     * @param $filename string Original Filename.
     * @return bool
     */
    public function setFile($tmp_name, $filename)
    {
        $up = wp_upload_bits($filename, null, file_get_contents($tmp_name));

        // Return FALSE on Error.
        if (!isset($up['file'])) return false;

        // Get the Filename relative to the upload dir.
        $up_dir = wp_upload_dir();
        $filename = substr($up['file'], strlen($up_dir['basedir']));

        // Save the file to Meta.
        update_post_meta($this->post->ID, $this->meta_key, $filename);

        return true;
    }

    public function removeFile()
    {
        delete_post_meta($this->post->ID, $this->meta_key);
    }

    /**
     * Does this file exist?
     * @return bool
     */
    public function isFile()
    {
        $path = $this->getFilePath();

        // Check for NULL
        if ($path === null) return false;

        // Check File Exists.
        if (is_file($path)) return true;
        else return false;
    }

    // ------- //
    // Private //
    // ------- //

    /**
     * Get the file extension from the supplied filename.
     * @param $filename
     * @return string
     */
    private static function _getFileExtension($filename)
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }


}