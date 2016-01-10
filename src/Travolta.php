<?php
namespace Travolta;

use PHPImageWorkshop\ImageWorkshop;

class Travolta {
    const WIDTH = 216; // min width
    const HEIGHT = 210; // max height

    protected $background_tmp;
    protected $background_tmp_gif = "%s.gif";
    protected $travolta_gif = "%s/src/_resource/travolta.gif";
    //protected $file_output_dir = "%s/files/";
    protected $file_output_gif = "%s/files/%s.gif";
    protected $file_output_name;

    public function __construct($root_dir, $background_tmp) {
        $this->background_tmp = $background_tmp;
        $this->file_output_name = substr(md5_file($this->background_tmp), 0, 5).rand(0,100);
        $this->file_output_gif = sprintf($this->file_output_gif, $root_dir, $this->file_output_name);
        $this->background_tmp_gif = sprintf($this->background_tmp_gif, $this->file_output_name);
        //$this->file_output_dir = sprintf($this->file_output_dir, $root_dir);
        $this->travolta_gif = sprintf($this->travolta_gif, $root_dir);
    }

    private function getBackground() {
        // create image
        $layer = ImageWorkshop::initFromPath($this->background_tmp);

        // resize
        //$newLargestSideWidth = self::WIDTH; // %
        //$conserveProportion = true;
        $layer->resizeInPixel(null, self::HEIGHT, true); // We can ignore the other params ($positionX, $positionY, $position)
        //$layer->resizeByNarrowSideInPixel($newLargestSideWidth, $conserveProportion);

        // crop
        //$layer->cropMaximumInPercent(0, 0, 'MM');

        $createFolders = false;
        $backgroundColor = null; // transparent, only for PNG (otherwise it will be white if set null)
        $imageQuality = 70; // useless for GIF, usefull for PNG and JPEG (0 to 100%)

        $layer->save("/tmp/", $this->background_tmp_gif, $createFolders, $backgroundColor, $imageQuality);

        $file_name = null;
        if(file_exists($this->background_tmp))
            $file_name = $this->background_tmp;

        return $file_name;
    }

    private function deleteBackground() {
        @unlink($this->background_tmp);
        @unlink("/tmp/".$this->background_tmp_gif);
    }

    public function createGif() {
        //$command = "convert %s null: \( %s -coalesce \) -gravity center -geometry +1+56 -layers composite -set dispose background %s";
        $command = "convert %s null: \( %s -coalesce \) -gravity center -geometry +1+0 -layers composite -set dispose background %s";
        $command = sprintf($command, '/tmp/'.$this->background_tmp_gif, $this->travolta_gif, $this->file_output_gif);
        exec($command, $result);

        $gif_name = null;
        if(file_exists($this->file_output_gif))
            $gif_name = $this->file_output_name;

        return $gif_name;
    }

    public function exec() {
        $background_file = $this->getBackground();
        $gif_name = $this->createGif();
        $this->deleteBackground();

        return $gif_name;
    }

    public static function getFilesOrderByAdded($dir) {
        $ignored = array('.', '..', '.svn', '.htaccess');

        $files = array();
        foreach (scandir($dir) as $file) {
            if (in_array($file, $ignored)) continue;
            $files[$file] = filemtime($dir . '/' . $file);
        }

        arsort($files);
        $files = array_keys($files);

        return ($files) ? $files : false;
    }
}
?>
