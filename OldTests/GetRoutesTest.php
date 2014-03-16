<?php
 

 
 
class CommentTest extends Slim_Framework_TestCase
{
	//Tests with default datatase ( db_herewecode.sql )
	public function testGetCommentWithId()
    {
		// Comment existing 
        $this->get('/rest/api/v0.1/comment/1');
        $this->assertEquals(200, $this->response->status());
        $this->assertSame('{"idComment":"1","idMember":"1","date":"2013-12-31","text":"Je kiffe le MacDo =D"}', $this->response->body());		
    } 
	zz
}