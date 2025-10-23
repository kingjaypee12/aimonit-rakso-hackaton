<!-- Recording Modal -->
<div x-show="recordingModal" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    
    <!-- Background overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50" @click="recordingModal = false"></div>
    
    <!-- Modal content -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
             @click.stop>
            
            <!-- Modal header -->
            <div class="flex items-center justify-between p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">
                    🎤 Record Live Class
                </h3>
                <button @click="recordingModal = false" 
                        class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Modal body -->
            <div class="p-6">
                <!-- Recording status -->
                <div class="text-center mb-6">
                    <div id="recording-status" class="text-sm text-gray-500 mb-4">Ready to record</div>
                    
                    <div id="recording-indicator" class="hidden mb-4">
                        <div class="inline-flex items-center px-4 py-2 bg-red-100 text-red-800 rounded-full">
                            <div class="w-3 h-3 bg-red-500 rounded-full mr-2 animate-pulse"></div>
                            <span class="font-medium">Recording...</span>
                            <span id="recording-timer" class="ml-2">00:00</span>
                        </div>
                    </div>
                    
                    <div class="space-x-4">
                        <button id="record-btn" 
                                onclick="startRecording()"
                                class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                            </svg>
                            Start Recording
                        </button>
                        
                        <button id="stop-btn" 
                                onclick="stopRecording()"
                                style="display: none;"
                                class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                            </svg>
                            Stop Recording
                        </button>
                    </div>
                </div>
                
                <!-- Audio visualizer -->
                <div class="mb-6">
                    <canvas id="audio-visualizer" 
                            class="w-full h-20 bg-gray-100 rounded-lg border"
                            style="display: none;"></canvas>
                </div>
                
                <!-- Recording information -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">Audio Recording Only</p>
                            <p>This will record audio from your microphone and save it for later download. The recording will be processed and made available in the lesson files.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Upload progress -->
                <div id="upload-progress" class="mt-6" style="display: none;">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-3"></div>
                            <span class="text-blue-800">Saving recording...</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal footer -->
            <div class="flex justify-end space-x-3 p-6 border-t bg-gray-50">
                <button @click="recordingModal = false" 
                        class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>