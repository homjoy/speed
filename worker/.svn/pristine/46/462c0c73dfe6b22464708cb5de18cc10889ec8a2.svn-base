<?php
namespace Worker\Package\Extractor;

use Worker\Package\Utils\RegexMatcher;
use Worker\Package\Utils\Utils;

/**
 *
 * Class TC58Extractor
 * @package Worker\Package\Extractor
 */
class TC58Extractor
{
    protected $matcher = null;
    protected $crawler;

    public function __construct($html)
    {
        $this->crawler = new \Symfony\Component\DomCrawler\Crawler($html);
        $this->matcher = new RegexMatcher($html);
    }


    public function resume(){
        $resume = array();

        $resume['username'] = $this->get('.name');
        $sexAge = str_replace('（','',$this->get('.sexAge'));
        $sexAge = explode('，',str_replace('）','',$sexAge));
        $resume['sex'] = $sexAge[0];
        $resume['age'] = $sexAge[1];


        $resume['phone'] = $this->get('.summ .ored');

        $resume['update_time'] = $this->get('.time');
        $resume['deliverycount'] = $this->get('#deliverycount');
        $resume['downcount'] = $this->get('#downcount');

        $resume['expect'] = array(
            'job' => $this->get('.expectTitle h2'),
        );

        $resume['post_job'] = array(
            'job' => $this->get('.applyPos.f-58'),
            'post_time' => $this->get('.deliverTime'),
        );



        $resume['academic'] = $this->get($this->crawler->filter('.expectDetail li')->eq(0));
        $resume['work_year'] = $this->get($this->crawler->filter('.expectDetail li')->eq(2));
        $resume['location'] = $this->get($this->crawler->filter('.expectDetail li')->eq(4));
        $resume['hometown'] = $this->get($this->crawler->filter('.expectDetail li')->eq(6));


        $resume['expect'] = array(
            'job'=>$this->get('.expectDetail:nth-child(1) li.pl0'),
            'work_place'=>$this->get('.expectDetail:nth-child(1) li:nth-child(3)'),
            'salary'=>$this->get('.expectDetail:nth-child(1) li.br0'),
        );

        $resume['self_assessment'] = $this->get('div.expectInfo > div.intrCon');

        $resume['self_tags'] = $this->crawler->filter('.cbright .fl')->each(function(\Symfony\Component\DomCrawler\Crawler $node){
            return $node->text();
        });


        $resume['work_exp_summary'] = $this->get('.addexpe .modtab span');
        $resume['work_exp'] = Utils::matchAll($html,'/<h4>(?<company>.*?)<\/h4>[\s\S.]*?工作时间：<\/span><span[\s\S.]*?>(?<start_time>.*?)-(?<end_time>.*?)<span[\s\S.]*?experTime\'>\[(?<total_time>.*?)\][\s\S.]*?薪资水平：<\/span><span[\s\S.]*?>(?<salary>.*?)<\/span>[\s\S.]*?在职职位：<\/span><span[\s\S.]*?>(?<job>.*?)<\/span>[\s\S.]*?工作职责：<\/span>[\s\S.]*?<span[\s\S.]*?>(?<respon>.*?)<\/span>/m');

        $resume['edu_exp'] = Utils::matchAll($this->html('.addeduc'),'/<ul\s*class=\"summ\">[\s\S.]*?<li>(?<start_time>.*?)-(?<end_time>.*?)<\/li>[\s\S.]*?<li>(?<school>.*?)<\/li>[\s\S.]*?<li[\s\S.]*?>(?<major>.*?)<\/li>[\s\S.]*?<\/ul>/m');


        $resume['language_ability'] = Utils::matchAll($this->html('.addlan'),'/<p\sclass=\"pst2\">\s*<span\sclass=\"sth\">(?<name>.*?)：<\/span>\s*<span\s*class=\"std\">\s*<span>(?<level_say>.*?)<\/span><span>(?<level_write>.*?)<\/span>\s*<span>(?<exam>.*?)<\/span>\s*<\/span>\s*<\/p>/m');

        $resume['cert'] = Utils::matchAll($this->html('.addcert'),'/<p\sclass=\"pst2\">\s*<span\sclass=\"sth\">(?<name>.*?)<\/span>\s*<span\sclass=\"std\">(?<get_time>.*?)<\/span>.*?<\/p>/m');

        $resume['skills'] = Utils::matchAll($this->html('.addAbility'),'/<p\sclass=\"pst\"><span\sclass=\"sth\">(?<name>.*?)：<\/span><span\sclass=\"std\"><span>(?<level>.*?)<\/span><span>(?<used_time>.*?)<\/span><\/span><\/p>/m');
        $resume['pictures'] = $this->crawler->filter('.addshowme .myphoto li img')->each(function(\Symfony\Component\DomCrawler\Crawler $node){
            return $node->attr('_src');
        });

        return $resume;
    }

    protected function get($selector,$text = true)
    {
        if($selector instanceof \Symfony\Component\DomCrawler\Crawler){
            $node = $selector;
        }else{
            $node = $this->crawler->filter($selector);
        }

        if(empty($node)||count($node) == 0){
            return $text ? '' : null;
        }
        return $text ? rtrim(trim($node->text())) : $node;
    }

    protected function html($selector)
    {
        $node = $this->get($selector,false);
        if(empty($node)|| count($node) == 0){
            return '';
        }
        return $node->html();
    }

    protected function getByClass($cls)
    {
        return $this->matcher->one('/class=\"'.$cls.'\".*?>(?<data>.*?)<\/.*?>/m');
    }

    protected function getById($id)
    {
        return $this->matcher->one('/id=\"'.$id.'\".*?>(?<data>.*?)<\/.*?>/m')
        || $this->matcher->one('/id=\"'.$id.'\".*?value=\"(?<data>.*?)\".*?>/m');
    }
}