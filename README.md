# PHP Web Crawler

## Overview

This is a simple web crawler written in PHP. The crawler can be used to retrieve information from web pages, search for patterns, and store the crawled data in JSON format.

## Technologies and Concepts

- PHP
- cURL
- DOMDocument
- JSON

## Setup

1. Make sure you have XAMPP installed in your system.
2. Clone the repository: `git clone https://github.com/ahmedobaid23/WE_Assignment2.git` in a folder known as htdocs inside xampp folder.
3. Open XAMPP Control Panel and start Apache module.
4. Open your browser and type "http://localhost/WE_Assignment2/index.php".
5. Press Enter.

## Functionality

### URL Queue

- The URL queue is managed through the $hyperlinks array, where hyperlinks are added during crawling, starting with the seed URL.

### Crawling

- The htmlRequest function sends HTTP requests to URLs in the $hyperlinks queue, retrieves HTML content using cURL, and loads it into a DOMDocument for further processing.

### HTML Parsing

- HTML parsing is implemented using the DOMDocument class in PHP, extracting relevant information such as title, meta tags, and paragraphs from crawled pages.

### URL Extraction

- Hyperlinks are extracted from the crawled HTML and added to the URL queue through the $hyperlinks array during the initial crawl.

### Depth Limit

- The depth limit is controlled by the $currentDepth variable, ensuring the crawler does not exceed a certain depth level from the seed URL.

### Output

- Extracted information, including title, meta description, and paragraphs, is displayed during crawling. The data is also stored in the $storage array and later written to a JSON file.

### Content Search Module

- The searchInJSON function searches for a specified string within the crawled content and displays URLs containing the search string along with the matched content.

### Robots.txt Compliance

- This crawler is in compliance with robots.txt. The URLs disallowed for all users are stored in the $patterns array. Those URLs have a specific pattern and they are being filtered via the rootTextMatching function.

### Error Handling

- Error handling is implemented in the htmlRequest function to handle situations where a page cannot be fetched, and an error message is displayed if HTML loading fails.

## Bonus Features

- Filtering: Filtering is implemented through the rootTextMatching function, excluding certain URLs based on specific criteria defined in the $patterns array.
- Persistent Storage: Crawled data is stored persistently in a JSON file named 'scraped_data.json' using file_put_contents.
- Advanced Search Features: The current implementation supports basic string matching in the search module, but more advanced search features, such as case sensitivity or regular expressions, are not implemented.

## Output

- The scraped data is written to 'scraped_data.json.'

## Author

Ahmed Obaidullah
