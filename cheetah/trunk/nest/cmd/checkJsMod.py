#!/usr/bin/python
import os
import re

def getAllJsFiles(jsfilelist, path):
	files = os.listdir(path)
	for f in files:
		pf = path+'/'+f
		if (os.path.isdir(pf)):
			if (f[0] == '.' or f == '.svn'):
				pass
			else:
				getAllJsFiles(jsfilelist, pf)
		if (os.path.isfile(pf) and f.endswith('.js') and f != 'fml.js'):
			jsfilelist.append(pf)
	return jsfilelist

def checkIsNote(sfile, sstr):
	scontent = open(sfile)
	sc = open(sfile, 'r').read()
	for k,line in enumerate(scontent):
		if (line.find(sstr) > -1):
			if (re.search(r'//.*'+sstr, line) == None and re.search('/\*.*'+line+'.*\*/', sc, re.DOTALL) == None):
				print 'ERROR in file: '+sfile+'; in line:'+str(k)+'; CONSOLE.LOG NOT DELETE: '+line

def checkMod(sfile):
	sc = open(sfile, 'r').read();
	defReqMods = [] 
	for line in open(sfile):
		if (line.find('fml.define') > -1):
			defObj = re.findall(r'fml\.define.*\[(.*)\]', line)
			if (defObj != []):
				defReqMods = [m.strip(' \'') for m in defObj[0].split(',')]
			break;
	reqMods = [m.strip(' \'') for m in re.findall(r'require\((.*)\)', sc)]
	if (len(defReqMods) == 0 or defReqMods[0] == ''):
		if (len(reqMods) > 0):
			print 'ERROR in file: ' + sfile + '... No define mods: ' + str(reqMods)
		return
	if (len(defReqMods) != len(reqMods)):
		print 'ERROR in file: ' + sfile + '... define mods NOT EQUAL require mods'
	for mod in defReqMods:
		if (mod not in reqMods and mod.split('/')[-1] not in reqMods):
			print 'ERROR in file:' + sfile + '...No require mod: ' + mod
	for mod in reqMods:
		if (mod not in defReqMods):
			if (mod in [m.split('/')[-1] for m in defReqMods]):
				print 'WARNING in file: ' + sfile + '...define mod not INDENTICAL with require mod: ' + mod
			else:
				print 'ERROR in file: ' + sfile + '...No define mod: ' + mod
	print
	
def check(jsfilelist):
	print '---------check console.log-------'
	for f in jsfilelist:
		checkIsNote(f, 'console.log')
	print '---------check mod--------------'
	for f in jsfilelist:
		checkMod(f)

#print getAllJsFiles([],'.')
#checkIsNote('./app/checkLogin.js', 'console.log')
check(getAllJsFiles([], '../web/script-ss'))
