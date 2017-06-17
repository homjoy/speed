<?php
namespace Atom\Modules\A;

class Documents extends \Frame\Module {

	/**
	 * <h3>这是一段注释 **** a a </h3>
	 *
	 * <br/>
	 * <pre>
	 * 参数说明：
	 * </pre>
	 *
	 */
    public function run() {
		$parser = \Atom\Package\Common\CommentParser::instance();
		$documents = $parser->getDocuments(null,array('a/','bad/','common/'));
		$this->response->setBody($documents);
    }

}