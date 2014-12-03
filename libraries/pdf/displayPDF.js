/*
'use strict';
//
// Fetch the PDF document from the URL using promises
//
PDFJS.getDocument(pdfFile).then(function(pdf) {
    // Using promise to fetch the page
    pdf.getPage(1).then(function(page) {
	var scale = 1;
	var viewport = page.getViewport(scale);
	//
	// Prepare canvas using PDF page dimensions
	//
	var canvas = document.getElementById('pdf-viewer');
	var context = canvas.getContext('2d');
	canvas.height = viewport.height;
	canvas.width = viewport.width;
	//
	// Render PDF page into canvas context
	//
	var renderContext = {
	    canvasContext: context,
	    viewport: viewport
	};
	page.render(renderContext);
    });
});
*/

//
// Disable workers to avoid yet another cross-origin issue (workers need
// the URL of the script to be loaded, and dynamically loading a cross-origin
// script does not work).
//
// PDFJS.disableWorker = true;
//
// In cases when the pdf.worker.js is located at the different folder than the
// pdf.js's one, or the pdf.js is executed via eval(), the workerSrc property
// shall be specified.
//
// PDFJS.workerSrc = '../../build/pdf.worker.js';

var pdfDoc = null,
pageNum = 1,
pageRendering = false,
pageNumPending = null,
scale = 0.6;

/**
 * Get page info from document, resize canvas accordingly, and render page.
 * @param num Page number.
 */
function renderPage(num,scale) {
    pageRendering = true;
    // Using promise to fetch the page
    pdfDoc.getPage(num).then(function(page) {
	var viewport = page.getViewport(scale);
	canvas.height = viewport.height;
	canvas.width = viewport.width;
	// Render PDF page into canvas context
	var renderContext = {
	    canvasContext: ctx,
	    viewport: viewport
	};
	var renderTask = page.render(renderContext);
	// Wait for rendering to finish
	renderTask.promise.then(function () {
	    pageRendering = false;
	    if (pageNumPending !== null) {
		// New page rendering is pending
		renderPage(pageNumPending,scale);
		pageNumPending = null;
	    }
	});
    });
    // Update page counters
    document.getElementById('page_num').textContent = pageNum;
}
/**
 * If another page rendering in progress, waits until the rendering is
 * finised. Otherwise, executes rendering immediately.
 */
function queueRenderPage(num,scale) {
    
    if (pageRendering) {
	pageNumPending = num;
    } else {
	renderPage(num,scale);
    }
}
/**
 * Displays previous page.
 */
function onPrevPage() {
    if (pageNum <= 1) {
	return;
    }
    pageNum--;
    queueRenderPage(pageNum,scale);
}
/**
 * Displays next page.
 */
function onNextPage() {
    if (pageNum >= pdfDoc.numPages) {
	return;
    }
    pageNum++;
    queueRenderPage(pageNum,scale);
}

function onZoomIn() {
    scale = scale * 1.2;
    console.log(scale);
    queueRenderPage(pageNum,scale);
}
function onZoomOut() {
    scale = scale / 1.2;
    console.log(scale);
    queueRenderPage(pageNum,scale);
}
/**
 * Asynchronously downloads PDF.
 */
PDFJS.getDocument(pdfFile).then(function (pdfDoc_) {
    document.getElementById('prev').addEventListener('click', onPrevPage);
    document.getElementById('next').addEventListener('click', onNextPage);
    document.getElementById('zoom-in').addEventListener('click', onZoomIn);
    document.getElementById('zoom-out').addEventListener('click', onZoomOut);
    canvas = document.getElementById('pdf-viewer');
    ctx = canvas.getContext('2d');
    pdfDoc = pdfDoc_;
    document.getElementById('page_count').textContent = pdfDoc.numPages;
    // Initial/first page rendering
    renderPage(pageNum,scale);
}); 