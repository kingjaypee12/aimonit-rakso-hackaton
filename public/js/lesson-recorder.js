// Lesson Audio Recording Functionality
class LessonRecorder {
    constructor() {
        this.mediaRecorder = null;
        this.audioChunks = [];
        this.isRecording = false;
        this.stream = null;
        this.recordingStartTime = null;
        this.recordingTimer = null;
        this.audioContext = null;
        this.analyser = null;
        this.visualizer = null;
    }

    checkBrowserSupport() {
        // Check for MediaRecorder support
        if (!window.MediaRecorder) {
            console.error('MediaRecorder not supported in this browser');
            return false;
        }

        // Check for getUserMedia support
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            console.error('getUserMedia not supported in this browser');
            return false;
        }

        // Check for basic audio support
        const audio = document.createElement('audio');
        const canPlayWebM = audio.canPlayType('audio/webm') !== '';
        const canPlayMP4 = audio.canPlayType('audio/mp4') !== '';
        
        if (!canPlayWebM && !canPlayMP4) {
            console.warn('Limited audio format support detected');
        }

        return true;
    }

    getBestMimeType() {
        // List of MIME types in order of preference
        const mimeTypes = [
            'audio/webm;codecs=opus',
            'audio/webm',
            'audio/mp4',
            'audio/mp4;codecs=mp4a.40.2',
            'audio/mpeg',
            'audio/wav',
            'audio/ogg;codecs=opus',
            'audio/ogg'
        ];

        // Find the first supported MIME type
        for (const mimeType of mimeTypes) {
            if (MediaRecorder.isTypeSupported(mimeType)) {
                console.log(`Using MIME type: ${mimeType}`);
                return mimeType;
            }
        }

        // Fallback to default if none are explicitly supported
        console.warn('No preferred MIME type supported, using default');
        return '';
    }

    async startRecording() {
        try {
            // Check browser support first
            if (!this.checkBrowserSupport()) {
                return;
            }

            // Get the best MIME type for recording
            const mimeType = this.getBestMimeType();

            // Request microphone access
            this.stream = await navigator.mediaDevices.getUserMedia({ 
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    autoGainControl: true
                } 
            });

            // Create MediaRecorder with optimal settings
            const options = mimeType ? { mimeType } : {};
            this.mediaRecorder = new MediaRecorder(this.stream, options);

            // Setup event handlers
            this.mediaRecorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                    this.audioChunks.push(event.data);
                }
            };

            this.mediaRecorder.onstop = () => {
                this.processRecording();
            };

            // Start recording
            this.mediaRecorder.start(1000); // Collect data every second
            this.isRecording = true;
            this.recordingStartTime = Date.now();
            
            // Update UI
            this.updateRecordingUI();
            this.startTimer();
            this.startVisualizer();

        } catch (error) {
            console.error('Error starting recording:', error);
            this.showError('Failed to start recording. Please check your microphone permissions.');
        }
    }

    stopRecording() {
        if (this.mediaRecorder && this.isRecording) {
            this.mediaRecorder.stop();
            this.isRecording = false;
            
            // Stop all tracks
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
            }
            
            // Update UI
            this.updateStoppedUI();
            this.stopTimer();
            this.stopVisualizer();
        }
    }

    async processRecording() {
        if (this.audioChunks.length === 0) {
            this.showError('No audio data recorded');
            return;
        }

        try {
            // Create blob from audio chunks
            const audioBlob = new Blob(this.audioChunks, { 
                type: this.mediaRecorder.mimeType || 'audio/webm' 
            });
            
            // Calculate duration
            const duration = Math.round((Date.now() - this.recordingStartTime) / 1000 / 60 * 100) / 100;
            
            // Show upload progress
            this.showUploadProgress();
            
            // Create FormData for upload
            const formData = new FormData();
            formData.append('audio_file', audioBlob, `lesson-recording-${Date.now()}.webm`);
            formData.append('duration_minutes', Math.ceil(duration));
            
            // Upload to server
            const response = await fetch('/admin/lessons/upload-recording', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                const result = await response.json();
                this.handleUploadSuccess(result);
            } else {
                throw new Error('Upload failed');
            }

        } catch (error) {
            console.error('Error processing recording:', error);
            this.showError('Failed to save recording. Please try again.');
        } finally {
            // Reset for next recording
            this.audioChunks = [];
        }
    }

    startTimer() {
        const timerElement = document.getElementById('recording-timer');
        if (!timerElement) return;

        this.recordingTimer = setInterval(() => {
            const elapsed = Math.floor((Date.now() - this.recordingStartTime) / 1000);
            const minutes = Math.floor(elapsed / 60);
            const seconds = elapsed % 60;
            timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }, 1000);
    }

    stopTimer() {
        if (this.recordingTimer) {
            clearInterval(this.recordingTimer);
            this.recordingTimer = null;
        }
    }

    startVisualizer() {
        if (!this.stream) return;

        try {
            this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
            this.analyser = this.audioContext.createAnalyser();
            const source = this.audioContext.createMediaStreamSource(this.stream);
            source.connect(this.analyser);

            this.analyser.fftSize = 256;
            const bufferLength = this.analyser.frequencyBinCount;
            const dataArray = new Uint8Array(bufferLength);

            const canvas = document.getElementById('audio-visualizer');
            if (!canvas) return;

            const canvasCtx = canvas.getContext('2d');
            const WIDTH = canvas.width;
            const HEIGHT = canvas.height;

            const draw = () => {
                if (!this.isRecording) return;

                requestAnimationFrame(draw);

                this.analyser.getByteFrequencyData(dataArray);

                canvasCtx.fillStyle = 'rgb(240, 240, 240)';
                canvasCtx.fillRect(0, 0, WIDTH, HEIGHT);

                const barWidth = (WIDTH / bufferLength) * 2.5;
                let barHeight;
                let x = 0;

                for (let i = 0; i < bufferLength; i++) {
                    barHeight = (dataArray[i] / 255) * HEIGHT;

                    canvasCtx.fillStyle = `rgb(${barHeight + 100}, 50, 50)`;
                    canvasCtx.fillRect(x, HEIGHT - barHeight / 2, barWidth, barHeight);

                    x += barWidth + 1;
                }
            };

            draw();
        } catch (error) {
            console.warn('Audio visualizer not available:', error);
        }
    }

    stopVisualizer() {
        if (this.audioContext) {
            this.audioContext.close();
            this.audioContext = null;
        }
        
        // Clear canvas
        const canvas = document.getElementById('audio-visualizer');
        if (canvas) {
            const canvasCtx = canvas.getContext('2d');
            canvasCtx.clearRect(0, 0, canvas.width, canvas.height);
        }
    }

    updateRecordingUI() {
        // Update recording button
        const recordBtn = document.getElementById('record-btn');
        const stopBtn = document.getElementById('stop-btn');
        
        if (recordBtn) {
            recordBtn.style.display = 'none';
        }
        if (stopBtn) {
            stopBtn.style.display = 'inline-flex';
        }

        // Show recording indicator
        const recordingIndicator = document.getElementById('recording-indicator');
        if (recordingIndicator) {
            recordingIndicator.style.display = 'flex';
        }

        // Update status
        const statusElement = document.getElementById('recording-status');
        if (statusElement) {
            statusElement.textContent = 'Recording in progress...';
            statusElement.className = 'text-sm text-red-600 font-medium';
        }
    }

    updateStoppedUI() {
        // Update buttons
        const recordBtn = document.getElementById('record-btn');
        const stopBtn = document.getElementById('stop-btn');
        
        if (recordBtn) {
            recordBtn.style.display = 'inline-flex';
        }
        if (stopBtn) {
            stopBtn.style.display = 'none';
        }

        // Hide recording indicator
        const recordingIndicator = document.getElementById('recording-indicator');
        if (recordingIndicator) {
            recordingIndicator.style.display = 'none';
        }

        // Update status
        const statusElement = document.getElementById('recording-status');
        if (statusElement) {
            statusElement.textContent = 'Processing recording...';
            statusElement.className = 'text-sm text-blue-600 font-medium';
        }
    }

    showUploadProgress() {
        const uploadProgress = document.getElementById('upload-progress');
        if (uploadProgress) {
            uploadProgress.style.display = 'block';
        }
    }

    handleUploadSuccess(result) {
        // Hide upload progress
        const uploadProgress = document.getElementById('upload-progress');
        if (uploadProgress) {
            uploadProgress.style.display = 'none';
        }

        // Update the form with the audio file path
        const audioFileInput = document.querySelector('input[name="audio_file_path"]');
        if (audioFileInput && result.file_path) {
            // For Filament file upload, we need to trigger the proper events
            const event = new CustomEvent('audio-uploaded', {
                detail: {
                    file_path: result.file_path,
                    duration: result.duration
                }
            });
            document.dispatchEvent(event);
        }

        // Update duration field
        const durationField = document.querySelector('input[name="duration_minutes"]');
        if (durationField && result.duration) {
            durationField.value = result.duration;
            durationField.dispatchEvent(new Event('input', { bubbles: true }));
        }

        // Close modal
        const modal = document.getElementById('recording-modal');
        if (modal) {
            modal.style.display = 'none';
        }

        // Show success message
        this.showSuccess('Audio recording saved successfully! You can now save the lesson.');

        // Update status
        const statusElement = document.getElementById('recording-status');
        if (statusElement) {
            statusElement.textContent = 'Ready to record';
            statusElement.className = 'text-sm text-gray-500';
        }
    }

    showError(message) {
        // Create or update error notification
        const notification = document.createElement('div');
        notification.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4';
        notification.innerHTML = `
            <strong>Error:</strong> ${message}
            <button type="button" class="float-right" onclick="this.parentElement.remove()">×</button>
        `;
        
        const container = document.querySelector('.filament-form') || document.body;
        container.insertBefore(notification, container.firstChild);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    showSuccess(message) {
        // Create success notification
        const notification = document.createElement('div');
        notification.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4';
        notification.innerHTML = `
            <strong>Success:</strong> ${message}
            <button type="button" class="float-right" onclick="this.parentElement.remove()">×</button>
        `;
        
        const container = document.querySelector('.filament-form') || document.body;
        container.insertBefore(notification, container.firstChild);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    // Static method to check browser support
    static isBrowserSupported() {
        try {
            // Check MediaRecorder
            if (typeof window.MediaRecorder === 'undefined') {
                console.log('MediaRecorder not available - likely due to HTTP instead of HTTPS');
                return false;
            }
            
            // Check navigator.mediaDevices
            if (typeof navigator.mediaDevices === 'undefined') {
                console.log('navigator.mediaDevices not available - likely due to HTTP instead of HTTPS');
                return false;
            }
            
            // Check getUserMedia
            if (typeof navigator.mediaDevices.getUserMedia !== 'function') {
                console.log('getUserMedia not available');
                return false;
            }
            
            console.log('All browser features are supported');
            return true;
        } catch (error) {
            console.error('Error checking browser support:', error);
            return false;
        }
    }

    // Static method to get browser support details
    static getBrowserSupportDetails() {
        const support = {
            mediaRecorder: !!window.MediaRecorder,
            getUserMedia: !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia),
            webAudio: !!(window.AudioContext || window.webkitAudioContext)
        };
        
        support.overall = support.mediaRecorder && support.getUserMedia;
        
        return support;
    }
}

// Global instance
const lessonRecorder = new LessonRecorder();

// Global functions for modal interaction
function openRecordingModal() {
    const modal = document.getElementById('recording-modal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

function startRecording() {
    lessonRecorder.startRecording();
}

function stopRecording() {
    lessonRecorder.stopRecording();
}

function playAudio(button) {
    const audioSrc = button.dataset.audioSrc;
    if (audioSrc) {
        const audio = new Audio(audioSrc);
        audio.play();
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Debug: Log browser information
    console.log('Browser detection debug:');
    console.log('- MediaRecorder:', !!window.MediaRecorder);
    console.log('- navigator.mediaDevices:', !!navigator.mediaDevices);
    console.log('- getUserMedia:', !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia));
    console.log('- User Agent:', navigator.userAgent);
    
    // Check for browser support and disable record button if not supported
    const isSupported = LessonRecorder.isBrowserSupported();
    console.log('- Overall support:', isSupported);
    
    if (!isSupported) {
        // Log detailed support information
        const supportDetails = LessonRecorder.getBrowserSupportDetails();
        console.warn('Audio recording not supported. Support details:', supportDetails);
        
        // Find and disable the record button
        const recordButton = document.querySelector('[onclick*="openRecordingModal"]');
        if (recordButton) {
            recordButton.disabled = true;
            recordButton.style.opacity = '0.5';
            recordButton.style.cursor = 'not-allowed';
            
            // Check if it's an HTTPS issue
            const isHttpsIssue = window.location.protocol === 'http:' && window.MediaRecorder;
            
            if (isHttpsIssue) {
                recordButton.title = 'Audio recording requires HTTPS. Please access this site using HTTPS.';
                
                // Add click handler to show HTTPS error message
                recordButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    lessonRecorder.showError('Audio recording requires a secure connection (HTTPS). Please access this site using HTTPS instead of HTTP.');
                });
            } else {
                recordButton.title = 'Audio recording not supported in this browser. Please use Chrome, Firefox, Safari, or Edge.';
                
                // Add click handler to show browser error message
                recordButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    lessonRecorder.showError('Audio recording is not supported in this browser. Please use Chrome, Firefox, Safari, or Edge.');
                });
            }
            
            // Remove the onclick handler
            recordButton.removeAttribute('onclick');
        }
    } else {
        // Browser supports recording, request microphone permissions immediately
        requestMicrophonePermissions();
    }
});

// Function to request microphone permissions on page load
async function requestMicrophonePermissions() {
    try {
        console.log('Requesting microphone permissions...');
        
        // Request microphone access
        const stream = await navigator.mediaDevices.getUserMedia({ 
            audio: true 
        });
        
        // Stop the stream immediately - we just wanted to get permission
        stream.getTracks().forEach(track => track.stop());
        
        console.log('Microphone permissions granted');
        showPermissionStatus('Microphone access granted. You can now record audio.', 'success');
        
    } catch (error) {
        console.warn('Microphone permission denied or not available:', error);
        
        if (error.name === 'NotAllowedError') {
            showPermissionStatus('Microphone access denied. Please allow microphone access to record audio.', 'warning');
        } else if (error.name === 'NotFoundError') {
            showPermissionStatus('No microphone found. Please connect a microphone to record audio.', 'warning');
        } else {
            showPermissionStatus('Unable to access microphone. Please check your browser settings.', 'error');
        }
    }
}

function showPermissionStatus(message, type) {
    // Create permission status notification
    const notification = document.createElement('div');
    
    const bgColor = type === 'success' ? 'bg-green-100 border-green-400 text-green-700' :
                   type === 'warning' ? 'bg-yellow-100 border-yellow-400 text-yellow-700' :
                   'bg-red-100 border-red-400 text-red-700';
    
    notification.className = `fixed top-4 right-4 ${bgColor} border px-4 py-3 rounded shadow-lg z-50 max-w-md`;
    notification.innerHTML = `
        <div class="flex items-center justify-between">
            <span class="text-sm">${message}</span>
            <button type="button" class="ml-4 text-lg font-bold" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}