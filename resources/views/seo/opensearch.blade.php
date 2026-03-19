<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
    <ShortName>{{ $orgName }}</ShortName>
    <Description>Search listings on {{ $orgName }}</Description>
    <InputEncoding>UTF-8</InputEncoding>
    <Image width="16" height="16" type="image/x-icon">{{ url('/favicon.ico') }}</Image>
    <Url type="text/html" method="get" template="{{ url('/listings') }}?q={searchTerms}"/>
    <Url type="application/opensearchdescription+xml" rel="self" template="{{ url('/opensearch.xml') }}"/>
</OpenSearchDescription>
