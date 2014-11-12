    /**
     * Build base url whether you use a domain name or a direct path to the folder.
     * Supported HTTP and HTTPS
     * @return string
     */
    public static function Base_URL(){
        if (isset($_SERVER['HTTP_HOST'])){
            $base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
            $base_url .= '://'. $_SERVER['HTTP_HOST'];
            $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
        } else {
            $base_url = 'http://localhost/';
        }
        return $base_url;
    }