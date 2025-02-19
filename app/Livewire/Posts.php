<?php
namespace App\Livewire;
use Livewire\WithFileUploads;

use Livewire\Component;
use App\Models\Post;

class Posts extends Component
{
    use WithFileUploads;

    public $title, $description, $photo, $post_id;
    public $isOpen = 0;
    public $search;
    public $rowperPage = 10;
    /**
     * Render the component with the posts and pagination
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {

        // Return the view with posts and pagination data
        return view('livewire.posts',[
            'posts' => $this->search === null ?
            Post::latest()->paginate($this->rowperPage) :
            Post::latest()->where('title', 'like', '%'.$this->search.'%')->paginate($this->rowperPage)
        ] );
    }

    public function searchPosts()
    {
    $this->render();
    }


    /**
     * Open the modal for creating a new post
     *
     * @return void
     */
    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    /**
     * Open the modal for creating or editing a post
     *
     * @return void
     */
    public function openModal()
    {
        $this->isOpen = true;
    }

    /**
     * Close the modal
     *
     * @return void
     */
    public function closeModal()
    {
        $this->isOpen = false;
    }

    /**
     * Reset the input fields for the post
     *
     * @return void
     */
    private function resetInputFields()
    {
        $this->title = '';
        $this->description = '';
        $this->photo = '';
        $this->post_id = '';
    }

    /**
     * Store the post (either create or update)
     *
     * @return void
     */
    public function store()
    {
        $this->validate([
            'title' => 'required',
            'description' => 'required',
            'photo' => $this->post_id ? 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($this->photo && is_object($this->photo)) {
            $photoPath = $this->photo->store('posts', 'public');
        } else {
            $photoPath = $this->post_id ? Post::find($this->post_id)->photo : null;
        }

        // Create or update the post
        Post::updateOrCreate(['id' => $this->post_id], [
            'title' => $this->title,
            'description' => $this->description,
            'photo' => $photoPath,
        ]);

        // Flash success message to the session
        session()->flash('message',
            $this->post_id ? 'Post Updated Successfully.' : 'Post Created Successfully.');

        // Close the modal and reset the form fields
        $this->closeModal();
        $this->reset(['title', 'description', 'photo', 'post_id']);
    }

    /**
     * Edit the post by finding it by ID
     *
     * @param int $id
     * @return void
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $this->post_id = $id;
        $this->title = $post->title;
        $this->description = $post->description;
        $this->photo = $post->photo;

        $this->openModal();
    }

    /**
     * Delete the post by ID
     *
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        Post::find($id)->delete();
        session()->flash('message', 'Post Deleted Successfully.');
        $this->closeModal();
    }
}
