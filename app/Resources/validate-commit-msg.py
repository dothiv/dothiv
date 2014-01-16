#!/usr/bin/env python
"""
Git commit hook:
 .git/hooks/commit-msg
 
 Check commit message according to angularjs guidelines:
  * https://docs.google.com/document/d/1QrDFcIiPjSLDn3EL15IJygNPiHORgU1_OOAqWjiDU5Y/edit#
"""
 
import sys
import re
import unittest

class CommitMessageValidator(object):
 
    valid_commit_types = ['feat', 'fix', 'docs', 'style', 'refactor', 'test', 'chore', ]
    help_address = 'https://docs.google.com/document/d/1QrDFcIiPjSLDn3EL15IJygNPiHORgU1_OOAqWjiDU5Y/edit#'
    _quiet = False
    first_line_length_limit = 70
    other_line_length_limit = 100
    
    def __init__(self, lines):
        self.lines = lines
        
    def isValidCommitMessage(self):
        if len(self.lines) == 0:
            self.printError("Empty commit message")
            return False
     
        # first line
        line = self.lines[0]
        m = re.search('^(.*)\((.*)\): (.*)$', line)
     
        if not m or len(m.groups()) != 3:
            self.printError("First commit message line (header) does not follow format: type(scope): message")
            return False
        
        commit_type, commit_scope, commit_message = m.groups()
    
        if commit_message[-1] == ".":
            self.printError("Commit subject must not end with a dot (.)!")
            return False
    
        if re.match('^[A-Z]', commit_message):
            self.printError("Commit subject must not have a capital first letter!")
            return False
        
        if commit_type not in self.valid_commit_types:
            self.printError("Commit type not in valid ones: %s" % ", ".join(self.valid_commit_types))
            return False
     
        if len(self.lines) > 1 and self.lines[1].strip():
            self.printError("Second commit message line must be empty")
            return False
     
        if len(self.lines) > 2 and not self.lines[2].strip():
            self.printError("Third commit message line (body) must not be empty")
            return False
        
        if len(self.lines[0]) > self.first_line_length_limit:
            self.printError("First line should not be longer than %d characters. It is %d long." % (self.first_line_length_limit, len(self.lines[0])))
            return False
        
        for line in self.lines[1:]:
            if len(line) > self.other_line_length_limit:
                self.printError("Other lines should not be longer than %d characters. It is %d long." % (self.other_line_length_limit, len(line)))
                return False
         
        return True
    
    def quiet(self):
        self._quiet = True
        return self
    
    def printError(self, msg):
        if (self._quiet):
            return
        sys.stderr.write(msg + "\n")
        sys.stderr.write("Refer to commit guide: %s\n" % self.help_address)
        
    
class CommitMessageValidatorTest(unittest.TestCase):
    
    def testValidFormat(self):
        assert CommitMessageValidator(["test(valid-commit-msg): implement tests"]).quiet().isValidCommitMessage() == True
    
    def testNotEmpty(self):
        assert CommitMessageValidator([]).quiet().isValidCommitMessage() == False
        assert CommitMessageValidator("").quiet().isValidCommitMessage() == False
        assert CommitMessageValidator([""]).quiet().isValidCommitMessage() == False
        
    def testInvalidFormat(self):
        assert CommitMessageValidator(["bla"]).quiet().isValidCommitMessage() == False
        
    def testNoCapitalLetter(self):
        assert CommitMessageValidator(["test(valid-commit-msg): Implement tests"]).quiet().isValidCommitMessage() == False
        
    def testNotDotAtEnd(self):
        assert CommitMessageValidator(["test(valid-commit-msg): implement tests."]).quiet().isValidCommitMessage() == False

    def testValidType(self):
        assert CommitMessageValidator(["foo(valid-commit-msg): implement tests"]).quiet().isValidCommitMessage() == False
    
    def testEmptySecondLine(self):
        assert CommitMessageValidator(["foo(valid-commit-msg): implement tests"]).quiet().isValidCommitMessage() == False
        
    def testLengthFirstLine(self):
        assert CommitMessageValidator(["test(valid-commit-msg): the message of the first line should not be longer than 70 characters"]).quiet().isValidCommitMessage() == False
    
    def testLengthOtherLines(self):
        assert CommitMessageValidator(["test(valid-commit-msg): short first line", "", "Also other lines should not be longer than 100 characters as rendering the message without soft-wraps makes them hard to read"]).quiet().isValidCommitMessage() == False
    
    
if __name__ == "__main__":
    
    if len(sys.argv) == 1:
        unittest.main()
        sys.exit(0)
        
    with open(sys.argv[1]) as commit:
        lines = commit.readlines()
        v = CommitMessageValidator(lines)
        if (v.isValidCommitMessage()):
            sys.exit(0)
        else:
            sys.exit(1)
    