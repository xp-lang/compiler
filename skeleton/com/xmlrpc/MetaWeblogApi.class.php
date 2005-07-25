<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xml.xmlrpc.XmlRpcClient',
    'xml.xmlrpc.transport.XmlRpcHttpTransport',
    'io.File',
    'io.FileUtil',
    'util.MimeType',
    'text.encode.Base64'
  );

  /**
   * MetaWeblog API
   *
   * Example:
   * <code>
   *   $m= &new MetaWeblogApi('url', 'username', 'password');
   *   try(); {
   *     $links= &$m->getRecentPosts(5); // Get the 5 Recent posts
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit;
   *   }
   *   var_dump($links);
   * </code>
   *
   * @purpose Provide an API to Blogs with the MetaWeblog API
   * @see http://www.xmlrpc.com/metaWeblogApi
   */
  class MetaWeblogApi extends XmlRpcClient {
    var
      $url      = '',
      $username = '',
      $password = '',
      $blogid   = '';

    /**
     * Constructor.
     *
     * Note: blogid is only needed if the blog is hosted by blogger.com, 
     * in which case it is the unique blog id
     *
     * @access  public
     * @param   string url for the weblog you want to access
     * @param   string username for the weblog
     * @param   string password for the weblog
     * @param   int blogid of the weblog
     */
    function __construct($url, $username, $password, $blogid= '0') {
      parent::__construct(new XMLRPCHTTPTransport($url));
      $this->username= $username;
      $this->password= $password;
      $this->blogid= $blogid;
    }

    /**
     * Add a new post to the Blog
     *
     * @access  public
     * @param   string title of the post
     * @param   string link
     * @param   string description it is the body of the post
     * @param   bool publish if set false, post will be saved as draft, true is publish it
     * @return  string the postid of the new post
     */  
    function newPost($title= '', $link= '', $description= '', $publish= FALSE) {
      return $this->invoke(
        'metaWeblog.newPost',
        $this->blogid,
        $this->username,
        $this->password,
        array(
          'title'       => $title,
          'link'        => $link,
          'description' => $description
        ),
        $publish == TRUE ? 1 : 0
      );    
    }

    /**
     * Edit a post
     *
     * @access  public
     * @param   int postid the id of the post you want to edit
     * @param   string title of the post
     * @param   string link
     * @param   string description it is the body of the post
     * @param   bool publish if set false, post will be saved as draft, true is publish it
     * @return  bool true when post is successfull
     */  
    function editPost($postid, $title= '', $link= '', $description= '', $publish= FALSE) {
      $response= $this->invoke(
        'metaWeblog.editPost',
        $postid,
        $this->username,
        $this->password,
        array(
          'title'       => $title,
          'link'        => $link,
          'description' => $description
        ),
        (int)$publish
      );
      return $response[0];
    }

    /**
     * Get a post
     *
     * @access  public
     * @param   int postid
     * @return  array if posting
     */  
    function getPost($postid) {
      return $this->invoke(
        'metaWeblog.getPost',
        $postid,
        $this->username,
        $this->password
      );
    }

    /**
     * Add a file to the Blog
     *
     * @access  public
     * @param   &io.File file
     * @return  array url of the file
     */  
    function newMediaObject(&$file) {
      return $this->invoke(
        'metaWeblog.newMediaObject',
        $this->blogid,
        $this->username,
        $this->password,
        array(
          'name' => $file->getFileName(),
          'type' => MimeType::getByFilename($file->getFileName()),
          'bits' => Base64::encode(FileUtil::getContents($file))
        )
      );
    }

    /**
     * Get all categories of the blog
     *
     * @access  public
     * @return  array categories
     */  
    function getCategories() {
      return $this->invoke(
        'metaWeblog.getCategories',
        $this->blogid,
        $this->username,
        $this->password
      );
    }

    /**
     * Get recent post of the blog
     *
     * @access  public
     * @param   int number of posts to get
     * @return  array categories
     */  
    function getRecentPosts($numberofposts) {
      return $this->invoke(
        'metaWeblog.getRecentPosts',
        $this->blogid,
        $this->username,
        $this->password,
        $numberofposts
      );
    }
  }
?>
