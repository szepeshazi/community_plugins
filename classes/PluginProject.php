<?php

class PluginProject extends ElggObject {
	protected function initialise_attributes() {
		parent::initialise_attributes();

		$this->attributes['subtype'] = "plugin_project";
	}

	public function __construct($guid = null) {
		parent::__construct($guid);
	}

	public function incrementDownloadCount() {
		create_annotation($this->guid, 'download', 1, 'integer', 0, ACCESS_PUBLIC);
	}

	public function getDownloadCount() {
		return $this->countAnnotations('download');
	}

	public function saveImage($name, $title, $index) {

		if ($_FILES[$name]['error'] != 0) {
			return FALSE;
		}

		$info = $_FILES[$name];

		// delete original image if exists
		$options = array(
			'relationship_guid' => $this->getGUID(),
			'relationship' => 'image',
			'metadata_name_value_pair' => array('name' => 'project_image', 'value' => "$index")
		);
		if ($old_image = elgg_get_entities_from_relationship($options)) {
			if ($old_image[0] instanceof ElggFile) {
				$old_image[0]->delete();
			}
		}

		$image = new ElggFile();
		$prefix = "plugins/";
		$store_name_base = $prefix . strtolower($this->getGUID() . "_$name");
		$image->title = $title;
		$image->access_id = $this->access_id;
		$image->setFilename($store_name_base . '.jpg');
		$image->setMimetype('image/jpeg');
		$image->originalfilename = $info['name'];
		$image->project_image = $index; // used for deletion on replacement
		$image->save();

		$uf = get_uploaded_file($name);
		if (!$uf) {
			return FALSE;
		}
		$image->open("write");
		$image->write($uf);
		$image->close();

		add_entity_relationship($this->guid, 'image', $image->guid);

		// create a thumbnail
		if ($this->saveThumbnail($image, $store_name_base . '_thumb.jpg') != TRUE) {
			$image->delete();
			return FALSE;
		}

		return TRUE;
	}

	protected function saveThumbnail($image, $name) {
		try {
			$thumbnail = get_resized_image_from_existing_file($image->getFilenameOnFilestore(), 60, 60, true);
		} catch (Exception $e) {
			return FALSE;
		}

		$thumb = new ElggFile();
		$thumb->setMimeType('image/jpeg');
		$thumb->access_id = $this->access_id;
		$thumb->setFilename($name);
		$thumb->open("write");
		$thumb->write($thumbnail);
		$thumb->save();
		$image->thumbnail_guid = $thumb->getGUID();

		if (!$thumb->getGUID()) {
			$thumb->delete();
			return FALSE;
		}

		return TRUE;
	}

}
