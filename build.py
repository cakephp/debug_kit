#! /usr/bin/env python

import sys, os
import tarfile, zipfile, gzip, bz2
from optparse import OptionParser

"""
Builds packaged releases of DebugKit so I don't have to do things manually.

Excludes itself (build.py), .gitignore, .DS_Store and the .git folder from the archives.
"""
def main():
    parser = OptionParser();
    parser.add_option('-o', '--output-dir', dest="output_dir",
                      help="write the packages to DIR", metavar="DIR")
    parser.add_option('-p', '--prefix-name', dest="prefix",
                      help="prefix used for the generated files")
    parser.add_option('-k', '--skip', dest="skip", default="",
                      help="A comma separated list of files to skip")
    parser.add_option('-s', '--source-dir', dest="source", default=".",
                      help="The source directory for the build process")

    (options, args) = parser.parse_args()

    if options.output_dir == '' or options.output_dir == options.source:
        print 'Requires an output dir, and that output dir cannot be the same as the source one!'
        exit()

    # append .git and build.py to the skip files
    skip = options.skip.split(',')
    skip.extend(['.git', '.gitignore', '.DS_Store', 'build.py'])

    # get list of files in top level dir.
    files = os.listdir(options.source)

    os.chdir(options.source)

    # filter the files, I couldn't figure out how to do it in a more concise way.
    for f in files[:]:
        try:
            skip.index(f)
            files.remove(f)
        except ValueError:
            pass
    
    # make a boring tar file
    destfile = ''.join([options.output_dir, options.prefix])
    tar_file_name = destfile + '.tar'
    tar = tarfile.open(tar_file_name, 'w');
    for f in files:
        tar.add(f)
    tar.close()
    print "Generated tar file"

    # make the gzip
    if make_gzip(tar_file_name, destfile):
        print "Generated gzip file"
    else:
        print "Could not generate gzip file"
    
    # make the bz2
    if make_bz2(tar_file_name, destfile):
        print "Generated bz2 file"
    else:
        print "Could not generate bz2 file"

    # make the zip file
    zip_recursive(destfile + '.zip', options.source, files)
    print "Generated zip file\n"

def make_gzip(tar_file, destination):
    """
    Takes a tar_file and destination. Compressess the tar file and creates
    a .tar.gzip
    """
    tar_contents = open(tar_file, 'rb')
    gzipfile = gzip.open(destination + '.tar.gz', 'wb')
    gzipfile.writelines(tar_contents)
    gzipfile.close()
    tar_contents.close()
    return True

def make_bz2(tar_file, destination):
    """
    Takes a tar_file and destination. Compressess the tar file and creates
    a .tar.bz2
    """
    tar_contents = open(tar_file, 'rb')
    bz2file = bz2.BZ2File(destination + '.tar.bz2', 'wb')
    bz2file.writelines(tar_contents)
    bz2file.close()
    tar_contents.close()
    return True

def zip_recursive(destination, source_dir, rootfiles):
    """
    Recursively zips source_dir into destination.
    rootfiles should contain a list of files in the top level directory that 
    are to be included.  Any top level files not in rootfiles will be omitted
    from the zip file.
    """
    zipped = zipfile.ZipFile(destination, 'w', zipfile.ZIP_DEFLATED)

    for root, dirs, files in os.walk(source_dir):
        inRoot = False
        if root == source_dir:
            inRoot = True
        
        if inRoot:
            for d in dirs:
                try:
                    rootfiles.index(d)
                except ValueError:
                    dirs.remove(d)

        for f in files[:]:
            if inRoot:
                try:
                    rootfiles.index(f)
                except ValueError:
                    continue
            
            fullpath = os.path.join(root, f)
            zipped.write(fullpath)
    zipped.close()
    return destination


if __name__ == '__main__':
    main()