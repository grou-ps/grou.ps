# $Id: cphplib.spec,v 1.1.1.1 2004/11/08 10:56:58 alex Exp $
%define name cphplib

Summary: 		Cute PHP Library (cphplib) 
Name:			%{name}
Version:		0.48
Release:		1
License:		LGPL
Vendor:			meindlSOFT
Group:			Development/Languages
URL:			http://www.meindlsoft.com/cphplib.php
Source0:		http://prdownloads.sourceforge.net/cphplib/cphplib-%{version}.tar.gz
BuildRoot:		%{_tmppath}/root-%{name}-%{version}
Prefix:			%{_prefix}
Buildarch:		noarch
Requires:		php

%description
cphplib (Cute PHP library) is a small collection of classes for PHP. Purpose of the classes is to simplify functions for daily work with PHP. e.g. convertions, formating, DB session and so on...

%prep
%setup -q

%build
#nothing to do

%install
[ "%{buildroot}" != "/" ] && %{__rm} -rf %{buildroot}
%{__install} -d -m0755 %{buildroot}%{_datadir}/%{name}/counters
%{__install} -m0644 *.inc *.php %{buildroot}%{_datadir}/%{name}/
%{__install} -m0644 counters/* %{buildroot}%{_datadir}/%{name}/counters

%clean
[ "%{buildroot}" != "/" ] && %{__rm} -rf %{buildroot}

%files
%defattr(-,root,root)
%doc ChangeLog COPYRIGHT README LGPL TODO
%{_datadir}/%{name}/

%changelog
* Sun May 09 2004 Alexander Meindl <am@meindlsoft.com>
- first version of RPM package
