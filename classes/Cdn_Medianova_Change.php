<?php

class Cdn_Medianova_Change
{
    private $blog_url = null;
    private $cdn_url = null;
    private $dirs = null;     // directories
    private $excludes = []; // not excludes
    private $relative = false; // path
    private $https;

    public function setBlogUrl($data){
        $this->blog_url = $data;
    }

    public function setCdnUrl($data){
        $this->cdn_url = $data;
    }

    public function setDirs($data){
        $this->dirs = $data;
    }

    public function setExcludes($data){
        $this->excludes = $data;
    }

    public function setRelative($data){
        $this->relative = $data;
    }

    public function setHttps($data){
        $this->https = $data;
    }

    public function getBlogUrl() {
        return $this->blog_url;
    }

    public function getCdnUrl(){
        return $this->cdn_url;
    }

    public function getDirs(){
        return  $this->dirs;
    }

    public function getExcludes(){
        return $this->excludes;
    }

    public function getRelative(){
        return $this->relative;
    }

    public function getHttps(){
        return $this->https;
    }

    /**
     * exclude_asset
     *
     * @since   1.0.0
     * @change  1.0.0
     *
     * @param   string current value
     * @return  boolean  need to be excluded
     */

    protected function exclude_extensions( &$asset ) {

        foreach ( $this->getExcludes() as $file_type ) {
            if ( $file_type && stristr($asset, $file_type ) == true) {
                return true;
            }
        }
        return false;
    }

    /**
     * rechange_url
     *
     * @since   1.0.0
     * @change  1.0.0
     *
     * @param   string url
     * @return  string if url has //
     */

    protected function rechange_url( $url ) {
        return substr( $url, strpos( $url, '//' ));
    }

    /**
     * get_directory
     *
     * @since   1.0.0
     * @change  1.0.0
     *
     * @param   string url
     * @return  string if empty dir directory get default value
     */

    protected function get_directory() {

        $get_input = explode( ',', $this->getDirs() );

        if (empty( $this->getDirs() ) || count( $get_input ) < 1 ) {
            // default get this directories
            return 'wp\-content|wp\-includes';
        }

        return implode( '|', array_map( 'quotemeta', array_map( 'trim', $get_input ) ) );
    }

    /**
     * get_directory
     *
     * @since   1.0.0
     * @change  1.0.0
     *
     * @param   string url
     * @return  string if empty dir directory get default value
     */

    protected function get_dir_scope(){

        $dirs = $this->get_directory();

        return $dirs;
    }

    /**
     * get_dir_scope_regex_format
     *
     * @since   1.0.0
     * @change  1.0.0
     *
     * @param   string url
     * @return  string get regrex for directories
     */

    protected function get_dir_scope_regex_format() {

        $local_url = $this->getHttps()
            ? '(https?:|)'.$this->rechange_url( quotemeta( $this->getBlogUrl() ) )
            : '(http:|)'.$this->rechange_url( quotemeta( $this->getBlogUrl() ) ) ;

        return $local_url;
    }

    /**
     * check_https
     *
     * @since   1.0.0
     * @change  1.0.0
     *
     * @param   string url
     * @return  string if has http or https control
     */

    protected function check_https( $data ){

        if ( !$this->getHttps() && isset( $_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on' ) {
            return $data;
        } else {
            return false;
        }
    }

    /**
     * check_url
     *
     * @since   1.0.0
     * @change  1.0.0
     *
     * @param   string url
     * @return  string if url is not correct get current
     */

    protected function check_url( $data, $local_url, $subst_urls, $relative ){

        if ( strpos($data, '//') === 0 ) {
            return str_replace( $local_url, $this->getCdnUrl(), $data );
        }

        if ( ! $relative || strstr( $data, $local_url ) ) {
            return str_replace( $subst_urls, $this->getCdnUrl(), $data );
        }

        return $this->getCdnUrl() . $data[0];
    }

    /**
     * change_url
     *
     * @since   1.0.0
     * @change  1.0.0
     *
     * @param   string url
     * @return  string changing position of local url and cdn url
     */

    protected function change_url( &$data ) {

        if ( $this->exclude_extensions( $data[0] ) ) {
            return $data[0];
        }

        if ( is_admin_bar_showing() and array_key_exists('preview', $_GET) and $_GET['preview'] == 'true' )
        {
            return $data[0];
        }

        $local_url = $this->rechange_url( $this->getBlogUrl() );
        $subst_urls = [ 'http:'.$local_url ];

        if ( $this->https ) {
            $subst_urls = array(
                'http:'.$local_url,
                'https:'.$local_url,
            );
        }

        return $this->check_url( $data[0], $local_url, $subst_urls, $this->getRelative() );

    }

    /**
     * change
     *
     * @since   1.0.0
     * @change  1.0.0
     *
     * @param   array $data
     * @return  array  $data with regrex rule
     */

    public function change( $data ) {
        $this->check_https( $data );

        $direcotry = $this->get_dir_scope();
        $url_control = $this->get_dir_scope_regex_format();

        $my_regex = '#(?<=[(\"\'])';

        if ( $this->getRelative() ) {
            $my_regex .=  '(?:'.$url_control.')?';
        } else {
            $my_regex .= $url_control;
        }

        $my_regex .= '/(?:((?:'.$direcotry.')[^\"\')]+)|([^/\"\']+\.[^/\"\')]+))(?=[\"\')])#';

        $cdn_data = preg_replace_callback( $my_regex, [ $this, 'change_url' ], $data );

        return $cdn_data;
    }
}