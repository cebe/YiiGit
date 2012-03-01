<?php
/**
 * Represents a file in a git repository.
 *
 * @property-read AGitCommit[] $commits
 * @property-read AGitCommit $lastCommit
 *
 * @author CeBe <mail@cebe.cc>
 * @package packages.git
 */
class AGitFile extends CComponent {
	/**
	 * The files name
	 * @var string
	 */
	public $fileName;
	/**
	 * The branch this commit is on
	 * @var AGitBranch
	 */
	public $branch;
	/**
	 * Holds an array of commits that changed this file
	 * @var array
	 */
	protected $_commits;

	/**
	 * Constructor
	 * @param AGitBranch $branch the git branch
	 */
	public function __construct(AGitBranch $branch, $fileName) {
		$this->branch = $branch;
		// normalize filename to be relative to repository
		$path = rtrim($branch->repository->path, DIRECTORY_SEPARATOR);
		if (strncasecmp($path, $fileName, strlen($path))==0) {
			$fileName = substr($fileName, strlen($path)+1);
		}
		$this->fileName = $fileName;
	}

	/**
	 * Gets a list of commits that changed this file
	 * @return AGitCommit[] array of commits
	 */
	public function getCommits() {
		if ($this->_commits === null) {
			$this->_commits = array();
			foreach(explode("\n", $this->branch->repository->run('log --format="%H" '.$this->fileName)) as $commitHash) {
				$this->_commits[$commitHash] = $this->branch->getCommit($commitHash);
			}
		}
		return $this->_commits;
	}

	/**
	 * Gets the last commit that changed this file
	 * @return AGitCommit
	 */
	public function getLastCommit() {
		return reset($this->getCommits());
	}
}