<?php
class ModelToolNitroUpgrade extends ModelToolNitro {

  public function run_upgrade() {
    // NitroPack 2.1.1 -> NitroPack 2.2
    if (
        !empty($this->request->post['Nitro']['PageCache']['ClearCacheOnProductEdit']) && 
        $this->request->post['Nitro']['PageCache']['ClearCacheOnProductEdit'] == 'yes'
    ) {
        $this->update_nitro_product_cache_table();
    }
  }

  public function update_nitro_product_cache_table() {
    initNitroProductCacheDb();

    $exists_query = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "nitro_product_cache WHERE Field='expires';");
    if ($exists_query->num_rows == 1) return;

    $this->db->query("ALTER TABLE " . DB_PREFIX . "nitro_product_cache ADD COLUMN expires DATETIME;");
  }
}
